import { driver, type DriveStep, type Driver } from 'driver.js';
import 'driver.js/dist/driver.css';

let activeDriver: Driver | null = null;

function destroyActiveTour(): void {
    activeDriver?.destroy();
    activeDriver = null;
}

function scrollDevelopersIntoView(): void {
    document
        .getElementById('developers')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function buildSteps(): DriveStep[] {
    return [
        {
            element: '[data-tour="welcome-hero"]',
            popover: {
                title: 'Welcome to Find Developer',
                description:
                    'Browse vetted developer profiles, compare skills, and find a strong match for your team. Use “Browse developers” or scroll down when you are ready to explore the directory.',
                side: 'bottom',
                align: 'center',
            },
        },
        {
            element: '[data-tour="developer-subscribe-cta"]',
            popover: {
                title: 'Unlock contacts & CVs',
                description:
                    'Public listings are limited. Subscribers get developer phone numbers and resume (CV) links so you can reach the right people faster. Use the contact button to ask about subscription access.',
                side: 'bottom',
                align: 'center',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-search"]',
            popover: {
                title: 'Search developers',
                description:
                    'Type a name, email, or skill. Results update automatically after a short pause so you can refine without extra clicks.',
                side: 'bottom',
                align: 'start',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-filters"]',
            popover: {
                title: 'Filters',
                description:
                    'Open filters to narrow by job title, experience bands, skills, badges, availability, and more. Apply when you are done; the URL updates so you can share a filtered view.',
                side: 'left',
                align: 'start',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-view-toggle"]',
            popover: {
                title: 'Cards or table',
                description:
                    'Switch between card view for quick scanning and table view for dense comparison. Your choice is remembered on this device.',
                side: 'bottom',
                align: 'center',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-compare"]',
            popover: {
                title: 'Compare developers',
                description:
                    'Select two developers using the checkboxes on cards or in the table. When both are selected, Compare opens a side-by-side view. Clear resets your picks.',
                side: 'bottom',
                align: 'center',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-card-badges"]',
            popover: {
                title: 'Achievement badges',
                description:
                    'These are platform badges (for example hackathons, certifications, or milestones). Hover an icon on a card to see the badge name, or click it to open the Badges page for full descriptions. In table view, names appear in the Badges column on each row.',
                side: 'bottom',
                align: 'center',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
        {
            element: '[data-tour="developer-results"]',
            popover: {
                title: 'Open profiles',
                description:
                    'Click a card or the profile link in the table to open the full portfolio.',
                side: 'top',
                align: 'center',
            },
            onHighlightStarted: () => {
                scrollDevelopersIntoView();
            },
            onHighlighted: (_el, _step, { driver: d }) => {
                window.setTimeout(() => d.refresh(), 450);
            },
        },
    ];
}

function stepsWithPresentElements(): DriveStep[] {
    return buildSteps().filter((step) => {
        if (typeof step.element !== 'string') {
            return true;
        }
        return document.querySelector(step.element) !== null;
    });
}

/**
 * Starts the guided tour for the public welcome / developer directory page.
 * Waits briefly so async sections (e.g. Suspense) can mount tour anchors.
 */
export async function startWelcomeTour(): Promise<void> {
    await new Promise<void>((resolve) => {
        window.requestAnimationFrame(() => resolve());
    });
    await new Promise((r) => setTimeout(r, 500));

    let steps = stepsWithPresentElements();
    if (steps.length === 0 || steps.length < buildSteps().length) {
        await new Promise((r) => setTimeout(r, 600));
        steps = stepsWithPresentElements();
    }

    if (steps.length === 0) {
        return;
    }

    destroyActiveTour();

    activeDriver = driver({
        showProgress: true,
        smoothScroll: true,
        stagePadding: 8,
        stageRadius: 8,
        nextBtnText: 'Next',
        prevBtnText: 'Back',
        doneBtnText: 'Done',
        progressText: '{{current}} of {{total}}',
        popoverClass: 'welcome-tour-popover',
        onDestroyed: () => {
            activeDriver = null;
        },
    });

    activeDriver.setSteps(steps);
    activeDriver.drive(0);
}

export function destroyWelcomeTour(): void {
    destroyActiveTour();
}
