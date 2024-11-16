// useLeafletLocate.ts
import { ref, onMounted } from "vue";
import L from "leaflet";

export function useLeafletLocate(map: L.Map, options = {}) {
    const locateControl = ref<any>(null);

    const defaultOptions = {
        position: "topleft",
        drawCircle: true,
        follow: false,
        stopFollowingOnDrag: false,
        remainActive: false,
        markerClass: L.circleMarker,
        circleStyle: {
            color: "#136AEC",
            fillColor: "#136AEC",
            fillOpacity: 0.15,
            weight: 2,
            opacity: 0.5,
        },
        markerStyle: {
            color: "#136AEC",
            fillColor: "#2A93EE",
            fillOpacity: 0.7,
            weight: 2,
            opacity: 0.9,
            radius: 5,
        },
        followCircleStyle: {},
        followMarkerStyle: {},
        icon: "fa fa-map-marker",
        iconLoading: "fa fa-spinner fa-spin",
        circlePadding: [0, 0],
        metric: true,
        setView: true,
        keepCurrentZoomLevel: false,
        showPopup: true,
        strings: {
            title: "Show me where I am",
            popup: "You are within {distance} {unit} from this point",
            outsideMapBoundsMsg:
                "You seem located outside the boundaries of the map",
        },
        locateOptions: {
            maxZoom: Infinity,
            watch: true,
        },
    };

    const initializeLocateControl = () => {
        const mergedOptions = { ...defaultOptions, ...options };

        locateControl.value = L.control
            .locate(mergedOptions as L.Control.LocateOptions)
            .addTo(map);
    };

    const start = () => {
        locateControl.value?.start();
    };

    const stop = () => {
        locateControl.value?.stop();
    };

    const stopFollowing = () => {
        locateControl.value?.stopFollowing();
    };

    onMounted(() => {
        if (map) {
            initializeLocateControl();
        }
    });

    return {
        locateControl,
        start,
        stop,
        stopFollowing,
    };
}
