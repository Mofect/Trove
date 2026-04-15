/**
 * Trove Customizer: responsive tab control — single input, switch active theme_mod by device.
 */
(function ( api ) {
	"use strict";

	/**
	 * Trove option keys (pc/tablet/phone) -> Customizer preview frame keys (desktop/tablet/mobile).
	 *
	 * @see WP_Customize_Manager::get_previewable_devices()
	 */
	function troveDeviceToPreviewDevice(trove) {
		var map = { pc: "desktop", tablet: "tablet", phone: "mobile" };
		return map[trove] || null;
	}

	function setCustomizerPreviewDevice(trove) {
		var previewKey = troveDeviceToPreviewDevice(trove);
		if (
			!previewKey ||
			!api.previewedDevice ||
			typeof api.previewedDevice.get !== "function" ||
			typeof api.previewedDevice.set !== "function"
		) {
			return;
		}
		var list = api.settings && api.settings.previewableDevices;
		if (list && !Object.prototype.hasOwnProperty.call(list, previewKey)) {
			return;
		}
		try {
			if (api.previewedDevice.get() !== previewKey) {
				api.previewedDevice.set(previewKey);
			}
		} catch (err) {
			// Preview state may be unavailable in edge contexts.
		}
	}

	function parseSettings(root) {
		var raw = root.getAttribute("data-settings");
		if (!raw) {
			return null;
		}
		try {
			return JSON.parse(raw);
		} catch (e) {
			return null;
		}
	}

	function initOne(root) {
		var fieldType = root.getAttribute("data-field-type") || "text";
		var settingsMap = parseSettings(root);
		if (!settingsMap || !settingsMap.pc || !settingsMap.tablet || !settingsMap.phone) {
			return;
		}

		var input = root.querySelector(".trove-r-active-input");
		var tabs = root.querySelectorAll(".trove-r-tab");
		var active = "pc";
		var colorPickerInited = false;
		var settings = {};
		var remaining = 3;

		function readInputValue() {
			if (fieldType === "checkbox") {
				return !!input.checked;
			}
			if (fieldType === "number") {
				var n = parseFloat(input.value, 10);
				return isNaN(n) ? 0 : n;
			}
			return input.value;
		}

		function writeInputValue(v) {
			if (fieldType === "checkbox") {
				input.checked = !!v;
				return;
			}
			if (v === null || typeof v === "undefined") {
				v = "";
			}
			input.value = v;
		}

		function getSetting(device) {
			return settings[device] || null;
		}

		function commit() {
			var s = getSetting(active);
			if (!s) {
				return;
			}
			s.set(readInputValue());
		}

		function load(device) {
			var s = getSetting(device);
			if (!s) {
				return;
			}
			writeInputValue(s.get());
		}

		function activate(device) {
			if (device === active) {
				return;
			}
			commit();
			active = device;
			tabs.forEach(function (tab) {
				var on = tab.getAttribute("data-device") === device;
				tab.classList.toggle("is-active", on);
				tab.setAttribute("aria-selected", on ? "true" : "false");
			});
			load(device);
			setCustomizerPreviewDevice(device);
		}

		function initColorPicker() {
			if (fieldType !== "color" || colorPickerInited) {
				return;
			}
			colorPickerInited = true;
		}

		function wireUi() {
			tabs.forEach(function (tab) {
				tab.addEventListener("click", function (e) {
					e.preventDefault();
					var d = tab.getAttribute("data-device");
					if (d) {
						activate(d);
					}
				});
			});

			if (fieldType === "checkbox") {
				input.addEventListener("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set(!!input.checked);
					}
				});
			} else if (fieldType === "select") {
				input.addEventListener("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set(input.value);
					}
				});
			} else if (fieldType === "number") {
				input.addEventListener("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set(readInputValue());
					}
				});
				input.addEventListener("input", function () {
					var s = getSetting(active);
					if (s) {
						s.set(readInputValue());
					}
				});
			} else if (fieldType === "text") {
				input.addEventListener("input", function () {
					var s = getSetting(active);
					if (s) {
						s.set(input.value);
					}
				});
				input.addEventListener("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set(input.value);
					}
				});
			} else if (fieldType === "color") {
				input.addEventListener("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set(input.value);
					}
				});
			}

			["pc", "tablet", "phone"].forEach(function (dev) {
				var s = getSetting(dev);
				if (!s) {
					return;
				}
				s.bind(function () {
					if (dev === active) {
						load(dev);
					}
				});
			});

			load("pc");
			if (fieldType === "color") {
				initColorPicker();
			}
		}

		["pc", "tablet", "phone"].forEach(function (dev) {
			api(
				settingsMap[dev],
				function (setting) {
					settings[dev] = setting;
					remaining -= 1;
					if (remaining === 0) {
						wireUi();
					}
				}
			);
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", function () {
			document.querySelectorAll(".trove-responsive-tabs-control").forEach(initOne);
		});
	} else {
		document.querySelectorAll(".trove-responsive-tabs-control").forEach(initOne);
	}
})(wp.customize);
