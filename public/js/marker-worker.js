self.addEventListener('message', async (e) => {
    const { data, action } = e.data;
    
    if (action === 'processMarkers') {
        const CHUNK_SIZE = 500;
        const processedData = [];

        for (let i = 0; i < data.length; i += CHUNK_SIZE) {
            const chunk = data.slice(i, i + CHUNK_SIZE)
                .filter(item => isValidLatLng(item.lat, item.lng))
                .map(item => ({
                    type: "Feature",
                    geometry: {
                        type: "Point",
                        coordinates: [item.lng, item.lat]
                    },
                    properties: item
                }));
            processedData.push(...chunk);
            
            // Beri waktu browser untuk memproses chunk
            await new Promise(resolve => setTimeout(resolve, 0));
        }

        self.postMessage({
            action: 'markersProcessed',
            data: processedData
        });
    }
});

function isValidLatLng(lat, lng) {
    return [lat, lng].every(coord => coord !== null && !isNaN(coord)) &&
           lat >= -90 && lat <= 90 &&
           lng >= -180 && lng <= 180;
}