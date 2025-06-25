import { createServer } from 'node:http'
import { fileURLToPath } from 'node:url'
import { dirname, resolve } from 'node:path'

const __dirname = dirname(fileURLToPath(import.meta.url))

createServer(async (req, res) => {
    const url = req.url
    
    try {
        const { render } = await import('./bootstrap/ssr/entry-server.js')
        const { html, head, state } = await render(url)
        
        res.setHeader('Content-Type', 'application/json')
        res.end(JSON.stringify({ html, head, state }))
    } catch (e) {
        console.error(e.stack)
        res.statusCode = 500
        res.end(e.stack)
    }
}).listen(13714, () => {
    console.log('Node SSR server running at http://localhost:13714')
})
