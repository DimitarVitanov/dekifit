<template>
    <AppLayout title="Recipes" description="Browse our collection of high-protein, macro-friendly recipes designed for bodybuilders.">
        <!-- Hero -->
        <section class="relative pt-32 pb-16 px-6 overflow-hidden">
            <div class="absolute inset-0">
                <Lightfall 
                    :colors="['#10b981', '#34d399', '#6ee7b7']"
                    background-color="#0a0a0a"
                    :speed="0.3"
                    :streak-count="2"
                    :glow="1"
                    :opacity="0.4"
                    class="w-full h-full"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-dark/80 to-dark"></div>
            </div>
            
            <div class="relative z-10 max-w-4xl mx-auto text-center">
                <p class="text-emerald-400 text-xs uppercase tracking-[0.5em] font-semibold mb-6">Recipe Library</p>
                <h1 class="font-display text-5xl md:text-7xl text-off-white mb-8">
                    <SplitText 
                        text="Fuel That Performs" 
                        :delay="50"
                        animation-from="{ opacity: 0, y: 40 }"
                        animation-to="{ opacity: 1, y: 0 }"
                    />
                </h1>
                <p class="text-text-body text-lg max-w-2xl mx-auto leading-relaxed">
                    Delicious, macro-optimized recipes that make eating for performance a pleasure, not a chore.
                </p>
            </div>
        </section>

        <!-- Filters -->
        <section class="py-8 px-6 border-b border-emerald-500/10">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-warm-grey text-sm">Filter by:</span>
                    <button 
                        v-for="category in categories" 
                        :key="category"
                        @click="activeCategory = category"
                        class="px-4 py-2 rounded-full text-sm transition-all duration-300"
                        :class="activeCategory === category 
                            ? 'bg-emerald-500 text-white' 
                            : 'bg-dark-grey/50 text-warm-grey hover:text-emerald-400 hover:bg-dark-grey'"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>
        </section>

        <!-- Recipes Grid -->
        <section class="py-16 md:py-24 px-6">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link 
                        v-for="recipe in filteredRecipes" 
                        :key="recipe.title"
                        :href="recipe.href"
                        class="group block bg-dark-grey/30 border border-emerald-500/10 rounded-2xl overflow-hidden hover:border-emerald-500/30 transition-all duration-500"
                    >
                        <div class="aspect-[4/3] bg-gradient-to-br from-emerald-900/20 to-teal-900/20 relative overflow-hidden">
                            <Orb 
                                :hue="160" 
                                :hue-range="30"
                                class="w-full h-full opacity-40 group-hover:opacity-60 transition-opacity duration-500"
                            />
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="px-3 py-1 bg-emerald-500/20 backdrop-blur-sm text-emerald-300 text-xs rounded-full">{{ recipe.calories }} cal</span>
                                <span class="px-3 py-1 bg-teal-500/20 backdrop-blur-sm text-teal-300 text-xs rounded-full">{{ recipe.protein }}g protein</span>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <span class="px-3 py-1 bg-dark/60 backdrop-blur-sm text-warm-grey text-xs rounded-full">{{ recipe.time }}</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-emerald-400 text-xs uppercase tracking-wider">{{ recipe.category }}</span>
                                <span class="text-warm-grey">•</span>
                                <span class="text-warm-grey text-xs">{{ recipe.difficulty }}</span>
                            </div>
                            <h3 class="font-display text-xl text-off-white group-hover:text-emerald-300 transition-colors mb-2">{{ recipe.title }}</h3>
                            <p class="text-text-body text-sm line-clamp-2">{{ recipe.description }}</p>
                            
                            <div class="mt-4 pt-4 border-t border-emerald-500/10 grid grid-cols-3 gap-2 text-center">
                                <div>
                                    <p class="text-emerald-400 font-semibold">{{ recipe.protein }}g</p>
                                    <p class="text-warm-grey text-xs">Protein</p>
                                </div>
                                <div>
                                    <p class="text-teal-400 font-semibold">{{ recipe.carbs }}g</p>
                                    <p class="text-warm-grey text-xs">Carbs</p>
                                </div>
                                <div>
                                    <p class="text-cyan-400 font-semibold">{{ recipe.fat }}g</p>
                                    <p class="text-warm-grey text-xs">Fat</p>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
                
                <div v-if="filteredRecipes.length === 0" class="text-center py-16">
                    <p class="text-warm-grey text-lg">No recipes found in this category.</p>
                </div>
            </div>
        </section>

        <!-- Newsletter CTA -->
        <section class="py-20 md:py-32 px-6 bg-dark-warm/30">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-4xl md:text-5xl text-off-white mb-6">
                    <ShinyText text="Get New Recipes Weekly" :speed="3" class="text-gradient" />
                </h2>
                <p class="text-text-body text-lg mb-8">
                    Subscribe to receive new high-protein recipes, nutrition tips, and exclusive content directly to your inbox.
                </p>
                <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input 
                        type="email" 
                        placeholder="Enter your email"
                        class="flex-1 px-6 py-4 bg-dark-grey/50 border border-emerald-500/20 rounded-xl text-off-white placeholder-warm-grey focus:border-emerald-500/50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm tracking-[0.2em] uppercase font-semibold rounded-xl hover:from-emerald-400 hover:to-teal-500 transition-all duration-300 glow-emerald">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Lightfall from '@/components/vue-bits/Lightfall.vue';
import SplitText from '@/components/vue-bits/SplitText.vue';
import Orb from '@/components/vue-bits/Orb.vue';
import ShinyText from '@/components/vue-bits/ShinyText.vue';

const categories = ['All', 'Breakfast', 'Lunch', 'Dinner', 'Snacks', 'Post-Workout', 'Meal Prep'];
const activeCategory = ref('All');

const allRecipes = [
    {
        title: 'Protein Power Bowl',
        description: 'Grilled chicken, quinoa, roasted vegetables, and tahini dressing for the perfect post-workout meal.',
        category: 'Post-Workout',
        time: '25 min',
        difficulty: 'Easy',
        calories: 650,
        protein: 52,
        carbs: 45,
        fat: 22,
        href: '/recipes/protein-power-bowl',
    },
    {
        title: 'Anabolic French Toast',
        description: 'High-protein breakfast with egg whites, cinnamon, and sugar-free maple syrup.',
        category: 'Breakfast',
        time: '15 min',
        difficulty: 'Easy',
        calories: 420,
        protein: 38,
        carbs: 42,
        fat: 8,
        href: '/recipes/anabolic-french-toast',
    },
    {
        title: 'Lean Beef Stir-Fry',
        description: 'Quick and delicious stir-fry with lean beef, colorful vegetables, and low-sodium soy sauce.',
        category: 'Dinner',
        time: '20 min',
        difficulty: 'Medium',
        calories: 480,
        protein: 45,
        carbs: 28,
        fat: 18,
        href: '/recipes/lean-beef-stir-fry',
    },
    {
        title: 'Greek Yogurt Parfait',
        description: 'Layered Greek yogurt with berries, granola, and a drizzle of honey.',
        category: 'Breakfast',
        time: '5 min',
        difficulty: 'Easy',
        calories: 320,
        protein: 28,
        carbs: 38,
        fat: 6,
        href: '/recipes/greek-yogurt-parfait',
    },
    {
        title: 'Turkey Meatballs',
        description: 'Juicy turkey meatballs with marinara sauce, perfect for meal prep.',
        category: 'Meal Prep',
        time: '35 min',
        difficulty: 'Medium',
        calories: 380,
        protein: 42,
        carbs: 12,
        fat: 16,
        href: '/recipes/turkey-meatballs',
    },
    {
        title: 'Protein Ice Cream',
        description: 'Creamy frozen treat made with protein powder, frozen banana, and almond milk.',
        category: 'Snacks',
        time: '10 min',
        difficulty: 'Easy',
        calories: 220,
        protein: 25,
        carbs: 28,
        fat: 3,
        href: '/recipes/protein-ice-cream',
    },
    {
        title: 'Salmon & Asparagus',
        description: 'Omega-3 rich salmon fillet with roasted asparagus and lemon butter.',
        category: 'Dinner',
        time: '25 min',
        difficulty: 'Medium',
        calories: 520,
        protein: 48,
        carbs: 8,
        fat: 32,
        href: '/recipes/salmon-asparagus',
    },
    {
        title: 'Chicken Burrito Bowl',
        description: 'Tex-Mex inspired bowl with seasoned chicken, rice, beans, and fresh salsa.',
        category: 'Lunch',
        time: '30 min',
        difficulty: 'Medium',
        calories: 580,
        protein: 48,
        carbs: 52,
        fat: 16,
        href: '/recipes/chicken-burrito-bowl',
    },
    {
        title: 'Cottage Cheese Pancakes',
        description: 'Fluffy protein pancakes made with cottage cheese and oats.',
        category: 'Breakfast',
        time: '20 min',
        difficulty: 'Easy',
        calories: 380,
        protein: 32,
        carbs: 35,
        fat: 10,
        href: '/recipes/cottage-cheese-pancakes',
    },
];

const filteredRecipes = computed(() => {
    if (activeCategory.value === 'All') {
        return allRecipes;
    }
    return allRecipes.filter(recipe => recipe.category === activeCategory.value);
});
</script>
