const Scheme = () => {
	if (document.body.classList.contains("system")) {
		document.body.classList.remove("system")
		const darkModePreference = window.matchMedia("(prefers-color-scheme: dark)");
		if (darkModePreference) document.body.classList.add("dark")
		darkModePreference.addEventListener("change", () => document.body.classList.toggle("dark"));
	}
}

document.addEventListener("DOMContentLoaded", Scheme)
