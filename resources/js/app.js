import './bootstrap';

const THEME_KEY = 'theme';

function resolveTheme() {
	const stored = localStorage.getItem(THEME_KEY);
	if (stored === 'dark' || stored === 'light') {
		return stored;
	}

	return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setTheme(theme) {
	document.documentElement.classList.toggle('dark', theme === 'dark');
}

function updateThemeUi() {
	const isDark = document.documentElement.classList.contains('dark');
	const icon = document.getElementById('theme-icon');
	const label = document.getElementById('theme-label');
	const iconMobile = document.getElementById('theme-icon-mobile');

	if (icon) icon.textContent = isDark ? '☀️' : '🌙';
	if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
	if (iconMobile) iconMobile.textContent = isDark ? '☀️' : '🌙';
}

function toggleTheme() {
	const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
	localStorage.setItem(THEME_KEY, nextTheme);
	setTheme(nextTheme);
	updateThemeUi();
}

function initTheme() {
	setTheme(resolveTheme());
	updateThemeUi();

	const desktopToggle = document.getElementById('theme-toggle');
	const mobileToggle = document.getElementById('theme-toggle-mobile');

	if (desktopToggle) desktopToggle.addEventListener('click', toggleTheme);
	if (mobileToggle) mobileToggle.addEventListener('click', toggleTheme);

	const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
	mediaQuery.addEventListener('change', () => {
		if (!localStorage.getItem(THEME_KEY)) {
			setTheme(resolveTheme());
			updateThemeUi();
		}
	});
}

document.addEventListener('DOMContentLoaded', initTheme);
