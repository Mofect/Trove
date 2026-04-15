/**
 * Trove Customizer: section tabs (General / Design / …) — show only controls for the active tab.
 */
(function ( api ) {
	"use strict";

	function applyTabVisibility(sectionId, activeTab, map) {
		var sub = document.getElementById("sub-accordion-section-" + sectionId);
		if (!sub || !map) {
			return;
		}
		sub.querySelectorAll(":scope > li.customize-control").forEach(function (li) {
			if (li.querySelector(".trove-cz-section-tabs")) {
				li.classList.remove("trove-cz-tab-hidden");
				return;
			}
			var id = li.id || "";
			var prefix = "customize-control-";
			if (id.indexOf(prefix) !== 0) {
				return;
			}
			var controlId = id.slice(prefix.length);
			if (map[controlId] === undefined) {
				return;
			}
			if (map[controlId] === activeTab) {
				li.classList.remove("trove-cz-tab-hidden");
			} else {
				li.classList.add("trove-cz-tab-hidden");
			}
		});
	}

	function wireSection(sectionId, config) {
		var map = config.controls || {};
		var settingId = config.setting;
		var sub = document.getElementById("sub-accordion-section-" + sectionId);
		if (!sub) {
			return;
		}

		api(settingId, function (setting) {
			var tabs = sub.querySelector(".trove-cz-section-tabs");
			var hidden = null;
			if (tabs) {
				var li = tabs.closest("li");
				hidden = li ? li.querySelector('input[type="hidden"][data-customize-setting-link]') : null;
			}

			function refresh() {
				applyTabVisibility(sectionId, setting.get(), map);
			}

			if (tabs) {
				tabs.querySelectorAll(".trove-cz-section-tab").forEach(function (btn) {
					btn.addEventListener("click", function (e) {
						e.preventDefault();
						var tab = btn.getAttribute("data-trove-section-tab");
						if (tab) {
							setting.set(tab);
						}
					});
				});
			}

			setting.bind(function (newVal) {
				var v = newVal;
				if (tabs) {
					tabs.querySelectorAll(".trove-cz-section-tab").forEach(function (b) {
						var on = b.getAttribute("data-trove-section-tab") === v;
						b.classList.toggle("is-active", on);
						b.setAttribute("aria-selected", on ? "true" : "false");
					});
				}
				if (hidden) {
					hidden.value = v;
				}
				refresh();
			});

			refresh();
		});
	}

	api.bind("ready", function () {
		var data = window.troveCzSectionTabs;
		if (!data || !data.sections) {
			return;
		}
		Object.keys(data.sections).forEach(function (sectionId) {
			var cfg = data.sections[sectionId];
			wireSection(sectionId, cfg);
		});
	});
})(wp.customize);
