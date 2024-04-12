/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./assets/**/*.js",
		"./assets/**/*.css",
		"./templates/**/*.html.twig",
	],
	darkMode: "class",
	theme: {
		extend: {
			colors: {
				primary: {
					50: "#e8eefd",
					100: "#bacdf6",
					200: "#8eabee",
					300: "#6488e5",
					400: "#3c63d9",
					500: "#2a4fd3",
					600: "#2242BF",
					700: "#18309E",
					800: "#0F2280",
					900: "#08155E",
					950: "#040B3D"
				},
				gray: {
					50: "#fafafa",
					100: "#EBEBEC",
					200: "#dcdcde",
					300: "#AFB0B3",
					400: "#74757B",
					500: "#363840",
					600: "#303238",
					700: "#2a2a30",
					800: "#141417",
					900: "#111114",
					950: "#0b0b0d",
				}
			}
		}
	}
}
