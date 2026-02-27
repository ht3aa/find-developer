<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import SeoHead from '@/components/SeoHead.vue';
import DevelopersByAvailabilityTypeChart from '@/components/charts/DevelopersByAvailabilityTypeChart.vue';
import DevelopersByJobTitleChart from '@/components/charts/DevelopersByJobTitleChart.vue';
import DevelopersByLocationChart from '@/components/charts/DevelopersByLocationChart.vue';
import AverageSalaryByExperienceChart from '@/components/charts/AverageSalaryByExperienceChart.vue';

type LocationPoint = { label: string; count: number };
type AvailabilityPoint = { label: string; count: number };
type SalaryPoint = { years_of_experience: number; average_salary: number };
type JobTitlePoint = { label: string; count: number };

defineProps<{
    developersByLocation: LocationPoint[];
    developersByAvailabilityType: AvailabilityPoint[];
    averageSalaryByExperience: SalaryPoint[];
    developersByJobTitle: JobTitlePoint[];
}>();
</script>

<template>
    <SeoHead
        title="Developer charts"
        description="Explore developer statistics: locations, availability types, salary by experience, and job titles."
        canonical="/charts"
    />
    <Head>
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Navbar />

        <Hero
            badge="Statistics"
            title="Developer charts"
            description="Explore developer distribution by location, availability type, experience, and job title."
        />

        <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-2">
                <DevelopersByLocationChart :data="developersByLocation" />
                <DevelopersByAvailabilityTypeChart :data="developersByAvailabilityType" />
                <AverageSalaryByExperienceChart :data="averageSalaryByExperience" />
                <DevelopersByJobTitleChart :data="developersByJobTitle" />
            </div>
        </section>

        <Footer />
    </div>
</template>
