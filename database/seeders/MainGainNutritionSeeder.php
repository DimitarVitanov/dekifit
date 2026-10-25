<?php

namespace Database\Seeders;

use App\Models\NutritionPlan;
use Illuminate\Database\Seeder;

/**
 * Coach Dejan's "Main Gain" nutrition plan (from "Nutrition plan - D.B. Main Gain.pdf"),
 * shared with every client. Skipped if it already exists, so the coach's edits stay.
 *   php artisan db:seed --class=MainGainNutritionSeeder --force
 */
class MainGainNutritionSeeder extends Seeder
{
    public const TITLE = 'Main Gain nutrition plan';

    public function run(): void
    {
        $plan = NutritionPlan::firstOrCreate(
            ['title' => self::TITLE, 'user_id' => null, 'program_id' => null],
            ['description' => $this->description(), 'meals' => $this->meals()],
        );

        $this->command?->info($plan->wasRecentlyCreated ? 'Main Gain nutrition plan added.' : 'Main Gain nutrition plan already exists.');
    }

    private function description(): string
    {
        return <<<'TEXT'
        Meal options for building muscle, at roughly 2,300–3,000 kcal a day.

        Eat any 3 of these meals a day, in any order. They're high in calories and protein, so on heavy lifting or very active days you can add 1 snack. Want more or fewer calories? Just add or reduce the grams in each meal.

        • Include vegetables with every meal
        • Prefer cooking with olive oil
        • Check product labels (added sugar, palm oil, etc.)

        I don't like strictly counting calories or weighing food, because it can create an unhealthy approach to nutrition. Enjoy your food and keep it balanced. This plan goes together with regular training, which is essential for building muscle.

        Hope you enjoy it and reach new heights with every delicious bite ❤
        Dejan
        TEXT;
    }

    private function meals(): array
    {
        $meal = fn (string $name, string $label, array $items, ?string $note = null) => [
            'name' => $name,
            'time' => $label,
            'note' => $note,
            // Each item is 'Food' or ['Food', 'amount'].
            'items' => array_map(fn ($item) => is_array($item) ? ['food' => $item[0], 'amount' => $item[1]] : ['food' => $item, 'amount' => null], $items),
        ];
        $list = fn (string $name, array $foods) => $meal($name, 'Food list', $foods);

        return [
            $meal('Classic', 'Breakfast · option 1', [['Eggs (3 whole + 3 whites)', '6'], ['Bread: white, whole grain or a tortilla', '1 slice'], ['Cheese, sliced or grated', '30 g']],
                'Protein 40 g · Carbs 25 g · Fat 27 g · 509 kcal. For less fat, swap the cheese for 150 g Greek yogurt.'),
            $meal('Bodybuilder oats', 'Breakfast · option 2', [['Oats', '100 g'], ['Protein powder', '1 scoop'], 'Banana or frozen fruit', 'Cocoa powder', 'Stevia', 'Peanut butter (optional)'],
                'Protein 40 g · Carbs 100 g · Fat 16 g · 686 kcal (with peanut butter). For fewer carbs, use 80 g oats.'),
            $meal('"Baki" shake', 'Breakfast · option 3', [['Oats', '50 g'], ['Greek yogurt', '150 g'], ['Protein powder', '1 scoop'], 'Banana (preferably frozen)', 'Frozen fruit', ['Milk', '150 ml'], 'Stevia + ice cubes'],
                'Blend until thick. Protein 50 g · Carbs 75 g · Fat 10 g · 588 kcal.'),
            $meal('Anabolic pancakes', 'Breakfast · option 4', [['Oats, blended', '100 g'], ['Greek yogurt', '150 g'], ['Protein powder', '1 scoop'], ['Whole eggs', '2'], ['Milk', '100–150 ml'], 'Banana', 'Stevia', 'Baking powder'],
                'Blend and cook on low heat. Protein 63 g · Carbs 104 g · Fat 19 g · 820 kcal. Toppings: whipped cream, honey, boiled frozen fruit as jam, no-sugar-added jam or a small drizzle of peanut butter.'),
            $meal('French toast', 'Breakfast · option 5', [['Bread', '3–4 slices (150 g)'], ['Eggs', '2 whole + 3 whites'], ['Protein powder', '1 scoop'], ['Milk', '50–150 ml'], 'Cinnamon', 'Stevia'],
                'Mix everything, dip the bread and cook on low heat. Protein 68 g · Carbs 66 g · Fat 25 g · 822 kcal. Toppings: peanut butter, honey or whipped cream.'),

            $meal('Standard bodybuilder meal', 'Lunch · option 1', [['Meat: chicken, beef or fish', '200 g'], ['Potatoes, baked or boiled', '300–350 g'], 'Vegetables of choice'],
                'Protein 66 g · Carbs 63 g · Fat 11 g · 630 kcal.'),
            $meal('Loaded tortillas', 'Lunch · option 2', [['Tortillas', '2'], ['Chicken', '150 g'], ['Greek yogurt', '150 g'], ['Cooked rice', '100 g'], ['Grated cheese', '30 g'], 'Tomato'],
                'Wrap everything and bake at 200 °C for 7–10 minutes. Protein 82 g · Carbs 93 g · Fat 26 g · 952 kcal (185 kcal less with one tortilla, which is fine too).'),
            $meal('Stuffed peppers', 'Lunch · option 3', [], 'Prepared the traditional way.'),
            $meal('Chicken burger "Deki style"', 'Lunch · option 4', [['Burger buns', '2'], ['Grilled chicken breast', '150 g'], ['Cheddar cheese', '2 slices (56 g)'], 'Pickles'],
                'Sauce: light mayo + ketchup + Greek yogurt + black pepper (+30 kcal). Protein 80 g · Carbs 54 g · Fat 40 g · 911 kcal.'),
            $meal('Sweet & chili meal', 'Lunch · option 5', [['Chicken or turkey', '200 g'], ['Sweet potato, baked', '300 g'], 'Roasted carrots & broccoli', 'Sweet chili sauce'],
                'Protein 64 g · Carbs 63 g · Fat 12 g · 640 kcal.'),
            $meal('Moussaka', 'Lunch · option 6', [], 'Prepared the traditional way.'),
            $meal('"Anabolic fart"', 'Lunch · option 7', [], 'Traditional baked beans with sausage.'),

            $meal('Mushroom trip', 'Dinner · option 1', [['Beef', '200 g'], 'Mushrooms', 'Carrots', ['Rice, cooked', '200 g']],
                'Protein 47 g · Carbs 56 g · Fat 27 g · 708 kcal.'),
            $meal('Tuna salad', 'Dinner · option 2', ['Tuna (preferably Rio Mare)', 'Corn', ['Croutons (white bread)', '80 g'], 'Lettuce', 'Light mayo dressing', ['Greek yogurt', '150 g']],
                'Croutons: slice the bread and bake 5–10 minutes at 200 °C until golden brown. Can also be made as a wrap. Protein 35 g · Carbs 44 g · Fat 10 g · 418 kcal.'),
            $meal('Dejan pasta', 'Dinner · option 3', [['Pasta, uncooked', '150 g'], ['Greek yogurt', '150 g'], 'Light mayo', 'Ketchup', ['Chicken', '100 g']],
                'Mix, add cheese on top and bake 5–7 minutes at 250 °C. Protein 62 g · Carbs 111 g · Fat 8 g · 780 kcal.'),
            $meal('Meat "ballceps"', 'Dinner · option 4', ['Meatballs (beef or turkey)', 'Tomato sauce', 'Rice']),

            $meal('Snacks', 'Snacks · add 1 on active days', [
                ['Greek yogurt 150 g + 1 scoop protein + 30 g granola (no added sugar)', '350 kcal'],
                'Sugar-free protein chocolate',
                'Protein pudding',
                'Pretzel sticks (peanut flavour)',
                ['Peanut butter 40 g on 3 rice cakes + 1 banana', '450 kcal'],
                'Fruit salad',
                'Protein shake + banana',
            ], 'Yogurt bowl: protein 41 g · carbs 25 g · fat 8 g. Rice cakes: protein 12 g · carbs 57 g · fat 21 g.'),

            $list('Carbohydrates', ['Oatmeal', 'Cream of wheat', 'Brown rice', 'Couscous', 'Applesauce', 'Sweet potato', 'Whole wheat wraps', 'Potatoes', 'Corn', 'Pasta / spaghetti', 'Rice', 'Quinoa', 'Bread', 'Fruits']),
            $list('Proteins', ['Vegetables', 'Fruits', 'Eggs', 'Tuna', 'Chicken', 'Pork', 'Turkey breast', 'Fish']),
            $list('Dairy proteins', ['Milk', 'Protein chocolate', 'Greek yogurt', 'Protein pudding', 'Cheese / mozzarella / cottage cheese']),
            $list('Fats', ['Omega-3 supplements', 'Olive oil', 'Avocado', 'Seeds (sunflower, pumpkin)', 'Olives', 'Nuts (walnuts, almonds, peanuts)', 'Nut butters', 'Butter']),
        ];
    }
}
