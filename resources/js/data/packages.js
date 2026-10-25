// Fallback package data, used when the programs table is empty.
// Keep in sync with database/seeders/ProgramSeeder.php.
export const fallbackPackages = [
    {
        title: 'Basic',
        slug: 'start',
        price: '50',
        tagline: 'Basic plan, step by step.',
        features: [
            'Training program',
            'Exercise video library',
            'Basic nutrition plan',
            'Calories & macros',
            'Progress tracking',
            'Sunday check-in',
            'Email / message support',
        ],
    },
    {
        title: 'Pro',
        slug: 'pro',
        price: '100',
        highlight: true,
        tagline: 'Coaching via video calls.',
        features: [
            'Everything in Basic, plus:',
            'Fully personalized nutrition plan',
            'Weekly check-in',
            'Weekly progress review',
            'Training adjustments based on progress',
            'Nutrition adjustments',
            'Direct Instagram support',
            '1 video call per month',
            'Goal-specific programming: fat loss / muscle gain / recomposition',
            'Bonus exercise tips and form checks',
        ],
    },
    {
        title: 'Elite',
        slug: 'elite',
        price: '140',
        tagline: '1-on-1 premium online video call coaching.',
        features: [
            'Everything in Pro, plus:',
            '2 video calls per month',
            'Priority response',
            'More frequent check-ins',
            'Faster training / nutrition adjustments',
            'Detailed weekly progress analysis',
            'Personalized cardio / step recommendations',
            'Exercise technique / form feedback from videos',
            'Higher level of accountability',
            'Priority scheduling / support',
        ],
    },
];

export function toPackage(p) {
    return {
        id: p.id,
        title: p.title,
        slug: p.slug,
        price: Number(p.price),
        highlight: p.is_featured,
        tagline: p.excerpt,
        features: p.features || [],
    };
}

// "Everything in Basic, plus:" style lines introduce a list rather than being a feature.
export function isInherit(feature) {
    return /^everything in/i.test(feature);
}
