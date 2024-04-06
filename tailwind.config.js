const themer = require("tailwindcss-themer");


/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./assets/**/*.js",
		"./templates/**/*.html.twig",
	],
	safelist: [
		"dark",
		"light"
	],
	darkMode: "class",
	plugins: [
		require('@tailwindcss/forms'),
		themer({
			defaultTheme: {
				extend: {
					borderRadius: {
						DEFAULT: "6px"
					}
				}
			},
			themes: [
				{
					name: "light",
					extend: {
						colors: {
							primary: {
								DEFAULT: "#2A4FD3",
								dark: "#1E3BA3"
							},

							secondary: "#e6e6e6",

							placeholder: "#868686",
							outline: "#cccccc",

							surface: {
								DEFAULT: "#FFFFFF",
								variant: "#F1F1F1",
								contrast: "#e6e6e6",
							},

							on: {
								primary: "#ffffff",

								color: {
									light: "#fff",
									DEFAULT: "#F9F9F9",
								},

								surface: {
									DEFAULT: "#030712",
									variant: "#181C28",
									contrast: "#030712"
								},
							}
						}
					}
				},
				{
					name: "dark",
					extend: {
						colors: {
							primary: {
								DEFAULT: "#3b5dd2",
								dark: "#1E3BA3"
							},

							secondary: "#1F253B",

							placeholder: "#868686",
							outline: "#2d2f35",

							surface: {
								DEFAULT: "#191b21",
								variant: "#151619",
								contrast: "#1f2129",
							},

							on: {
								primary: {
									DEFAULT: "#ffffff",
									container: "#001a40"
								},

								color: {
									light: "#fff",
									DEFAULT: "#F9F9F9",
								},

								surface: {
									DEFAULT: "#FFFFFF",
									variant: "#ededed",
								},
							}
						}
					}
				}
			]
		})
	]
}
