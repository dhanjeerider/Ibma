/* ==========================================================
   Vegamovies Theme — Main Script
   ========================================================== */

(function () {
    "use strict";

    /* ----------------------------------------------------------
       Helpers
       ---------------------------------------------------------- */
    function $(selector) {
        return document.querySelector(selector);
    }

    function $all(selector) {
        return document.querySelectorAll(selector);
    }

    function toggleClass(el, className) {
        if (!el) return;
        el.classList.toggle(className);
    }

    function addClass(el, className) {
        if (!el) return;
        el.classList.add(className);
    }

    function removeClass(el, className) {
        if (!el) return;
        el.classList.remove(className);
    }

    /* ----------------------------------------------------------
       Mobile Menu Toggle
       Toggles the .open class on #primary-menu
       and updates aria-expanded on the toggle button.
       ---------------------------------------------------------- */
    function initMobileMenu() {
        var menuToggle = $("#menu-toggle");
        var primaryMenu = $("#primary-menu");

        if (!menuToggle || !primaryMenu) return;

        // Overlay element — created dynamically if not present
        var overlay = $("#mobile-menu-overlay");
        if (!overlay) {
            overlay = document.createElement("div");
            overlay.id = "mobile-menu-overlay";
            document.body.appendChild(overlay);
        }

        function openMenu() {
            addClass(primaryMenu, "open");
            menuToggle.setAttribute("aria-expanded", "true");
            addClass(overlay, "active");
        }

        function closeMenu() {
            removeClass(primaryMenu, "open");
            menuToggle.setAttribute("aria-expanded", "false");
            removeClass(overlay, "active");
        }

        function toggleMenu() {
            if (primaryMenu.classList.contains("open")) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        menuToggle.addEventListener("click", toggleMenu);

        // Close menu when overlay (backdrop) is clicked
        overlay.addEventListener("click", function () {
            closeMenu();
            closeSearch();
        });

        // Close menu on Escape key
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closeMenu();
                closeSearch();
            }
        });

        // Close menu when a nav link is clicked (single-page / ajax navigation)
        var navLinks = $all("#menu-main-navigation a");
        navLinks.forEach(function (link) {
            link.addEventListener("click", closeMenu);
        });
    }

    /* ----------------------------------------------------------
       Search Bar Toggle
       Toggles the .open class on .navbar-search
       and updates aria-expanded on the search-toggle button.
       ---------------------------------------------------------- */
    var searchBar = null;

    function closeSearch() {
        var toggle = $("#search-toggle");
        if (!searchBar) searchBar = $(".navbar-search");
        if (!searchBar) return;
        removeClass(searchBar, "open");
        if (toggle) toggle.setAttribute("aria-expanded", "false");
    }

    function initSearchToggle() {
        var searchToggle = $("#search-toggle");
        searchBar = $(".navbar-search");

        if (!searchToggle || !searchBar) return;

        searchToggle.addEventListener("click", function () {
            var isOpen = searchBar.classList.contains("open");
            if (isOpen) {
                closeSearch();
            } else {
                addClass(searchBar, "open");
                searchToggle.setAttribute("aria-expanded", "true");
                // Focus the input for accessibility
                var input = searchBar.querySelector(".search-input");
                if (input) {
                    setTimeout(function () { input.focus(); }, 50);
                }
            }
        });

        // Prevent closing when clicking inside the search bar
        searchBar.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

    /* ----------------------------------------------------------
       Read More / Read Less Button
       Toggles the .expanded class on #rmContent
       ---------------------------------------------------------- */
    function initReadMore() {
        var btn = $("#rmBtn");
        var content = $("#rmContent");

        if (!btn || !content) return;

        btn.addEventListener("click", function () {
            var expanded = content.classList.contains("expanded");
            if (expanded) {
                removeClass(content, "expanded");
                btn.textContent = "Read More";
                btn.setAttribute("aria-expanded", "false");
                // Scroll back to top of the wrapper smoothly
                var wrapper = btn.closest(".read-more-wrapper");
                if (wrapper) {
                    wrapper.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            } else {
                addClass(content, "expanded");
                btn.textContent = "Read Less";
                btn.setAttribute("aria-expanded", "true");
            }
        });

        // Set initial aria-expanded
        btn.setAttribute("aria-expanded", "false");
    }

    /* ----------------------------------------------------------
       Lazy Image Loading
       Uses IntersectionObserver to load images with data-src
       once they approach the viewport.
       ---------------------------------------------------------- */
    function initLazyImages() {
        var lazyImages = $all("img.lazy[data-src]");
        if (!lazyImages.length) return;

        if (!("IntersectionObserver" in window)) {
            // Fallback: load all images immediately
            lazyImages.forEach(function (img) {
                loadImage(img);
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        loadImage(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: "200px 0px" }
        );

        lazyImages.forEach(function (img) {
            observer.observe(img);
        });
    }

    function loadImage(img) {
        var src = img.getAttribute("data-src");
        if (!src) return;

        img.addEventListener("load", function () {
            var wrapper = img.closest(".lazy-wrapper");
            if (wrapper) {
                addClass(wrapper, "loaded");
            }
        });

        img.addEventListener("error", function () {
            // On error, still mark as loaded to hide spinner
            var wrapper = img.closest(".lazy-wrapper");
            if (wrapper) {
                addClass(wrapper, "loaded");
            }
        });

        img.src = src;
        img.removeAttribute("data-src");
    }

    /* ----------------------------------------------------------
       Active nav link highlighting
       Marks the current-menu-item based on window location
       ---------------------------------------------------------- */
    function initActiveNavLink() {
        var currentPath = window.location.pathname;
        var navLinks = $all("#menu-main-navigation a");

        navLinks.forEach(function (link) {
            try {
                var linkPath = new URL(link.href).pathname;
                if (linkPath === currentPath || linkPath === currentPath + "/") {
                    var li = link.closest("li");
                    if (li) {
                        addClass(li, "current-menu-item");
                    }
                }
            } catch (e) {
                // Ignore invalid URLs
            }
        });
    }

    /* ----------------------------------------------------------
       Sticky header shadow on scroll
       ---------------------------------------------------------- */
    function initStickyHeader() {
        var header = $("#site-header");
        if (!header) return;

        function onScroll() {
            if (window.scrollY > 10) {
                header.style.boxShadow = "0 2px 12px rgba(0,0,0,0.6)";
            } else {
                header.style.boxShadow = "none";
            }
        }

        window.addEventListener("scroll", onScroll, { passive: true });
    }

    /* ----------------------------------------------------------
       Horizontal category bar — drag to scroll on desktop
       ---------------------------------------------------------- */
    function initCategoryScroll() {
        var bar = $("#category-list .list-inline");
        if (!bar) return;

        var isDown = false;
        var startX;
        var scrollLeft;

        bar.addEventListener("mousedown", function (e) {
            isDown = true;
            startX = e.pageX - bar.offsetLeft;
            scrollLeft = bar.scrollLeft;
            bar.style.cursor = "grabbing";
        });

        bar.addEventListener("mouseleave", function () {
            isDown = false;
            bar.style.cursor = "";
        });

        bar.addEventListener("mouseup", function () {
            isDown = false;
            bar.style.cursor = "";
        });

        bar.addEventListener("mousemove", function (e) {
            if (!isDown) return;
            e.preventDefault();
            var x = e.pageX - bar.offsetLeft;
            var walk = (x - startX) * 1.5;
            bar.scrollLeft = scrollLeft - walk;
        });
    }

    /* ----------------------------------------------------------
       Init all on DOMContentLoaded
       ---------------------------------------------------------- */
    document.addEventListener("DOMContentLoaded", function () {
        initMobileMenu();
        initSearchToggle();
        initReadMore();
        initLazyImages();
        initActiveNavLink();
        initStickyHeader();
        initCategoryScroll();
    });
})();
