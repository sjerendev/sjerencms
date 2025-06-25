import express from 'express'
import { fileURLToPath } from 'url'
import { dirname, resolve } from 'path'
import { createServer as createViteServer } from 'vite'

const __dirname = dirname(fileURLToPath(import.meta.url))

async function createServer() {
    const app = express()

    const vite = await createViteServer({
        server: { middlewareMode: true },
        appType: 'custom'
    })

    app.use(vite.middlewares)

    app.use('*', async (req, res) => {
        const url = req.originalUrl
        try {
            const template = await vite.transformIndexHtml(url, `
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                    </head>
                    <body>
                        <div id="app"><!--ssr-outlet--></div>
                    </body>
                </html>
            `)

            const { render } = await vite.ssrLoadModule('/resources/js/entry-server.js')
            const { html, head } = await render(url)

            const finalHtml = template
                .replace('<!--ssr-outlet-->', html)
                .replace('</head>', `${head}</head>`)

            res.status(200).set({ 'Content-Type': 'text/html' }).end(finalHtml)
        } catch (e) {
            vite.ssrFixStacktrace(e)
            console.error(e)
            res.status(500).end(e.message)
        }
    })

    app.listen(3000, () => {
        console.log('Server running at http://localhost:3000')
    })
}

createServer()
