import theme from 'tailwindcss/defaultTheme'
import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
		theme: {
			extend: {
				colors: {
					primary: {
						...theme.colors.blue,
						50: '#f3f8fc',
						100: '#e7f0f7',
						200: '#c9dfee',
						300: '#9ac6df',
						400: '#64a7cc',
						500: '#408cb7',
						600: '#2f719a',
						700: '#275a7d',
						800: '#244d68',
						900: '#224158',
						950: '#172554',
					},
					custom: {
						...theme.colors.green,
						50: '#f2fbf9',
						100: '#d5f2eb',
						200: '#abe4d6',
						300: '#78d0be',
						400: '#4db4a2',
						500: '#339989',
						600: '#277a6f',
						700: '#22635b',
						800: '#204f4a',
						900: '#1e433e',
						950: '#0c2725',
					}
				}
			}
		}
}
