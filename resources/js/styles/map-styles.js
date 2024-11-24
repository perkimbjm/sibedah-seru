// styles/map-styles.js
export const loadMapStyles = () => {
    const styles = [
        {
            href: "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css",
            crossorigin: "anonymous",
        },
        "/css/fontawesome.css",
        {
            href: "https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css",
            crossorigin: "anonymous",
        },
        {
            href: "https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css",
            crossorigin: "anonymous",
        },
        {
            href: "https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css",
            crossorigin: "anonymous",
        },
        // Local CSS files don't need crossorigin
        "/css/locateControl.css",
        "/css/Control.Geocoder.css",
        "/css/iconLayers.css",
        "/css/leaflet.pm.css",
        "/css/Leaflet.PolylineMeasure.css",
        "/css/leaflet.toolbar.css",
        "/css/leaflet-sidepanel.css",
    ];

    styles.forEach((style) => {
        const link = document.createElement("link");
        link.rel = "stylesheet";

        if (typeof style === "string") {
            // Local file
            link.href = style;
        } else {
            // External file
            link.href = style.href;
            if (style.crossorigin) {
                link.crossorigin = style.crossorigin;
            }
        }

        document.head.appendChild(link);
    });
};
