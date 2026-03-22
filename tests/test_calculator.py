"""Unit tests for the baking cost calculator."""

import json
import os
import tempfile
import unittest

from baking_calculator.calculator import (
    calculate_raw_cost,
    calculate_selling_price,
    calculate_unit_cost,
    export_to_csv,
    generate_price_list,
    load_ingredients,
    load_recipes,
    round_to_nearest_5,
)


class TestLoadFunctions(unittest.TestCase):
    """Tests for loading JSON data files."""

    def test_load_ingredients(self):
        """Test that ingredients.json loads correctly."""
        base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
        filepath = os.path.join(base_dir, "baking_calculator", "ingredients.json")
        ingredients = load_ingredients(filepath)
        self.assertIsInstance(ingredients, dict)
        self.assertIn("all_purpose_flour", ingredients)
        self.assertEqual(ingredients["all_purpose_flour"]["unit"], "kg")
        self.assertEqual(ingredients["all_purpose_flour"]["cost_per_unit"], 45)

    def test_load_recipes(self):
        """Test that recipes.json loads correctly."""
        base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
        filepath = os.path.join(base_dir, "baking_calculator", "recipes.json")
        recipes = load_recipes(filepath)
        self.assertIsInstance(recipes, dict)
        self.assertIn("chocolate_brownies", recipes)
        self.assertEqual(recipes["chocolate_brownies"]["batch_yield"], 12)


class TestCalculateRawCost(unittest.TestCase):
    """Tests for calculate_raw_cost function."""

    def setUp(self):
        self.ingredients = {
            "flour": {"unit": "kg", "cost_per_unit": 50},
            "sugar": {"unit": "kg", "cost_per_unit": 40},
            "butter": {"unit": "kg", "cost_per_unit": 500},
            "eggs": {"unit": "piece", "cost_per_unit": 7},
        }

    def test_simple_recipe(self):
        """Test raw cost calculation for a simple recipe."""
        recipe = {
            "ingredients": {
                "flour": 0.5,    # 0.5 kg * 50 = 25
                "sugar": 0.2,    # 0.2 kg * 40 = 8
                "butter": 0.1,   # 0.1 kg * 500 = 50
            },
            "batch_yield": 10,
            "yield_unit": "pieces",
        }
        raw_cost = calculate_raw_cost(recipe, self.ingredients)
        self.assertAlmostEqual(raw_cost, 83.0)

    def test_single_ingredient(self):
        """Test raw cost with a single ingredient."""
        recipe = {
            "ingredients": {"eggs": 6},  # 6 * 7 = 42
            "batch_yield": 6,
            "yield_unit": "pieces",
        }
        raw_cost = calculate_raw_cost(recipe, self.ingredients)
        self.assertAlmostEqual(raw_cost, 42.0)

    def test_missing_ingredient_raises_error(self):
        """Test that a missing ingredient raises ValueError."""
        recipe = {
            "ingredients": {
                "flour": 0.5,
                "vanilla_essence": 0.01,  # not in ingredients
            },
            "batch_yield": 10,
            "yield_unit": "pieces",
        }
        with self.assertRaises(ValueError) as context:
            calculate_raw_cost(recipe, self.ingredients)
        self.assertIn("vanilla_essence", str(context.exception))


class TestCalculateUnitCost(unittest.TestCase):
    """Tests for calculate_unit_cost function."""

    def setUp(self):
        self.ingredients = {
            "flour": {"unit": "kg", "cost_per_unit": 50},
            "sugar": {"unit": "kg", "cost_per_unit": 40},
        }

    def test_unit_cost_divides_correctly(self):
        """Test that unit cost divides raw cost by batch yield."""
        recipe = {
            "ingredients": {
                "flour": 1.0,    # 1 kg * 50 = 50
                "sugar": 0.5,    # 0.5 kg * 40 = 20
            },
            "batch_yield": 10,
            "yield_unit": "pieces",
        }
        # Raw cost = 70, unit cost = 70 / 10 = 7.0
        unit_cost = calculate_unit_cost(recipe, self.ingredients)
        self.assertAlmostEqual(unit_cost, 7.0)

    def test_unit_cost_single_yield(self):
        """Test unit cost when batch yield is 1."""
        recipe = {
            "ingredients": {
                "flour": 0.3,    # 0.3 * 50 = 15
            },
            "batch_yield": 1,
            "yield_unit": "loaf",
        }
        unit_cost = calculate_unit_cost(recipe, self.ingredients)
        self.assertAlmostEqual(unit_cost, 15.0)


class TestCalculateSellingPrice(unittest.TestCase):
    """Tests for calculate_selling_price function."""

    def setUp(self):
        self.ingredients = {
            "flour": {"unit": "kg", "cost_per_unit": 100},
            "sugar": {"unit": "kg", "cost_per_unit": 50},
        }

    def test_selling_price_with_overhead_and_margin(self):
        """Test selling price includes overhead and margin correctly."""
        recipe = {
            "ingredients": {
                "flour": 1.0,    # 1 * 100 = 100
                "sugar": 1.0,    # 1 * 50 = 50
            },
            "batch_yield": 10,
            "yield_unit": "pieces",
            "overhead": {
                "gas_electricity": 50,      # 50 / 10 = 5 per unit
                "packaging_per_unit": 10,    # 10 per unit
                "labor_per_batch": 100,      # 100 / 10 = 10 per unit
            },
            "profit_margin_percent": 50,
        }
        # Raw cost per unit = 150 / 10 = 15
        # Overhead per unit = 5 + 10 + 10 = 25
        # Total cost per unit = 15 + 25 = 40
        # With 50% margin = 40 * 1.5 = 60
        # Rounded to nearest 5 = 60
        selling_price = calculate_selling_price(recipe, self.ingredients)
        self.assertEqual(selling_price, 60)

    def test_selling_price_rounds_to_nearest_5(self):
        """Test that selling price is rounded to nearest 5."""
        recipe = {
            "ingredients": {
                "flour": 0.5,    # 0.5 * 100 = 50
            },
            "batch_yield": 10,
            "yield_unit": "pieces",
            "overhead": {
                "gas_electricity": 10,       # 10 / 10 = 1 per unit
                "packaging_per_unit": 2,     # 2 per unit
                "labor_per_batch": 20,       # 20 / 10 = 2 per unit
            },
            "profit_margin_percent": 40,
        }
        # Raw cost per unit = 50 / 10 = 5
        # Overhead per unit = 1 + 2 + 2 = 5
        # Total cost per unit = 10
        # With 40% margin = 10 * 1.4 = 14
        # Rounded to nearest 5 = 15
        selling_price = calculate_selling_price(recipe, self.ingredients)
        self.assertEqual(selling_price, 15)


class TestRoundToNearest5(unittest.TestCase):
    """Tests for the round_to_nearest_5 utility function."""

    def test_exact_multiple(self):
        self.assertEqual(round_to_nearest_5(25), 25)

    def test_rounds_up(self):
        self.assertEqual(round_to_nearest_5(23), 25)

    def test_rounds_up_from_just_above(self):
        self.assertEqual(round_to_nearest_5(21), 25)

    def test_zero(self):
        self.assertEqual(round_to_nearest_5(0), 0)


class TestGeneratePriceList(unittest.TestCase):
    """Tests for generate_price_list function."""

    def test_generates_list_for_all_recipes(self):
        """Test that price list contains entries for all recipes."""
        ingredients = {
            "flour": {"unit": "kg", "cost_per_unit": 50},
        }
        recipes = {
            "recipe_a": {
                "description": "Recipe A",
                "batch_yield": 10,
                "yield_unit": "pieces",
                "ingredients": {"flour": 1.0},
                "overhead": {
                    "gas_electricity": 10,
                    "packaging_per_unit": 5,
                    "labor_per_batch": 20,
                },
                "profit_margin_percent": 30,
            },
            "recipe_b": {
                "description": "Recipe B",
                "batch_yield": 5,
                "yield_unit": "pieces",
                "ingredients": {"flour": 0.5},
                "overhead": {
                    "gas_electricity": 10,
                    "packaging_per_unit": 5,
                    "labor_per_batch": 20,
                },
                "profit_margin_percent": 40,
            },
        }
        price_list = generate_price_list(recipes, ingredients)
        self.assertEqual(len(price_list), 2)
        names = [item["name"] for item in price_list]
        self.assertIn("recipe_a", names)
        self.assertIn("recipe_b", names)

    def test_price_list_fields(self):
        """Test that each entry has all required fields."""
        ingredients = {
            "flour": {"unit": "kg", "cost_per_unit": 50},
        }
        recipes = {
            "test_recipe": {
                "description": "Test Recipe",
                "batch_yield": 10,
                "yield_unit": "pieces",
                "ingredients": {"flour": 1.0},
                "overhead": {
                    "gas_electricity": 10,
                    "packaging_per_unit": 5,
                    "labor_per_batch": 20,
                },
                "profit_margin_percent": 30,
            },
        }
        price_list = generate_price_list(recipes, ingredients)
        item = price_list[0]
        required_fields = [
            "name", "description", "raw_cost", "unit_cost",
            "selling_price", "profit_margin_percent", "batch_yield", "yield_unit",
        ]
        for field in required_fields:
            self.assertIn(field, item)


class TestExportToCSV(unittest.TestCase):
    """Tests for export_to_csv function."""

    def test_export_creates_file(self):
        """Test that export creates a valid CSV file."""
        price_list = [
            {
                "name": "test_item",
                "description": "Test Item",
                "raw_cost": 100.0,
                "unit_cost": 10.0,
                "selling_price": 15,
                "profit_margin_percent": 40,
                "batch_yield": 10,
                "yield_unit": "pieces",
            }
        ]
        with tempfile.NamedTemporaryFile(mode="w", suffix=".csv", delete=False) as f:
            output_path = f.name

        try:
            export_to_csv(price_list, output_path)
            self.assertTrue(os.path.exists(output_path))

            with open(output_path, "r") as f:
                lines = f.readlines()
            self.assertEqual(len(lines), 2)  # header + 1 data row
            self.assertIn("Test Item", lines[1])
        finally:
            os.unlink(output_path)


if __name__ == "__main__":
    unittest.main()
