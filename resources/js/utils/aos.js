export const loadAOS = async () => {
    const { default: AOS } = await import("aos");
    await import("aos/dist/aos.css");
    AOS.init();
};