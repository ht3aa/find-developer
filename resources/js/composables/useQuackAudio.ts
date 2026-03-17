/**
 * Shared quack audio instance. Loads once and reuses across all Duck components.
 */
let quackAudio: HTMLAudioElement | null = null;

export function useQuackAudio(): { playQuack: () => void } {
    if (!quackAudio) {
        quackAudio = new Audio('/sound/quack.mp3');
    }

    function playQuack(): void {
        if (!quackAudio) return;
        quackAudio.currentTime = 0;
        quackAudio.playbackRate = 1.5;
        quackAudio.play().catch(() => {
            // Ignore autoplay policy / user gesture requirements
        });
    }

    return { playQuack };
}
