/**
 * Trove Customizer: responsive tab control — single input, switch active theme_mod by device.
 */
(function (api) {
	"use strict";

	var controlsRoot =
		document.getElementById("customize-theme-controls") || document.body;
	var responsiveControlStates = [];
	var globalResponsiveDevice = "pc";

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

	function notifyPreviewResponsiveDevice(trove) {
		if (!api.previewer || typeof api.previewer.send !== "function") {
			return;
		}
		try {
			api.previewer.send("trove-responsive-device-change", { device: trove });
		} catch (err) {
			// Preview channel may be unavailable in edge contexts.
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

	function isValidResponsiveDevice(device) {
		return device === "pc" || device === "tablet" || device === "phone";
	}

	function syncAllResponsiveControls(device, sourceRoot) {
		if (!isValidResponsiveDevice(device)) {
			return;
		}
		globalResponsiveDevice = device;
		responsiveControlStates.forEach(function (state) {
			if (!state || !state.root || typeof state.activate !== "function") {
				return;
			}
			if (sourceRoot && state.root === sourceRoot) {
				return;
			}
			state.activate(device, true);
		});
	}

	function wireUnitSelect(root, settingsMap) {
		var unitSid = root.getAttribute("data-trove-unit-setting");
		var unitSel = root.querySelector(".trove-r-unit-select");
		if (!unitSid || !unitSel) {
			return;
		}
		api(unitSid, function (setting) {
			function syncFromSetting() {
				var v = setting.get();
				if (String(unitSel.value) !== String(v)) {
					unitSel.value = v;
				}
			}
			unitSel.addEventListener("change", function () {
				setting.set(unitSel.value);
			});
			setting.bind(syncFromSetting);
			syncFromSetting();
		});
	}

	function initOne(root) {
		if (!root || root.getAttribute("data-trove-r-wired") === "1") {
			return;
		}

		var fieldType = root.getAttribute("data-field-type") || "text";
		var settingsMap = parseSettings(root);
		if (!settingsMap || !settingsMap.pc || !settingsMap.tablet || !settingsMap.phone) {
			return;
		}

		var input = root.querySelector(".trove-r-active-input");
		if (!input) {
			return;
		}

		var tabs = root.querySelectorAll(".trove-r-tab");
		var active = "pc";
		var colorPickerInited = false;
		var settings = {};
		var remaining = 3;

		function readInputValue() {
			if (fieldType === "checkbox" || fieldType === "switch") {
				return !!input.checked;
			}
			if (fieldType === "number") {
				var n = parseFloat(input.value, 10);
				return isNaN(n) ? 0 : n;
			}
			return input.value;
		}

		function writeInputValue(v) {
			if (fieldType === "checkbox" || fieldType === "switch") {
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

		function activate(device, fromSync) {
			if (!isValidResponsiveDevice(device)) {
				return;
			}
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
			if (!fromSync) {
				setCustomizerPreviewDevice(device);
				notifyPreviewResponsiveDevice(device);
				syncAllResponsiveControls(device, root);
			}
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

			if (fieldType === "checkbox" || fieldType === "switch") {
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

			wireUnitSelect(root, settingsMap);

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

			root.setAttribute("data-trove-r-wired", "1");
			responsiveControlStates.push({
				root: root,
				activate: activate,
			});
			if (globalResponsiveDevice !== "pc") {
				activate(globalResponsiveDevice, true);
			}
		}

		["pc", "tablet", "phone"].forEach(function (dev) {
			api(settingsMap[dev], function (setting) {
				settings[dev] = setting;
				remaining -= 1;
				if (remaining === 0) {
					wireUi();
				}
			});
		});
	}

	function scanAndInit() {
		document.querySelectorAll(".trove-responsive-tabs-control").forEach(initOne);
	}

	if (api && typeof api.bind === "function") {
		api.bind("ready", function () {
			scanAndInit();
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", scanAndInit);
	} else {
		scanAndInit();
	}

	if (typeof MutationObserver !== "undefined" && controlsRoot) {
		var mo = new MutationObserver(function () {
			scanAndInit();
		});
		mo.observe(controlsRoot, { childList: true, subtree: true });
	}
})(wp.customize);
