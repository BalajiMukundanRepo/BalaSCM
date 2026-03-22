"""Core pricing engine for the baking cost calculator."""

import json
import csv
import math
import os


def load_ingredients(filepath):
    """Load ingredient prices from a JSON file.

    Args:
        filepath: Path to the ingredients JSON file.

    Returns:
        Dictionary of ingredients with their unit and cost_per_unit.
    """
    with open(filepath, "r") as f:
        return json.load(f)


def load_recipes(filepath):
    """Load recipes from a JSON file.

    Args:
        filepath: Path to the recipes JSON file.

    Returns:
        Dictionary of recipes with their details.
    """
    with open(filepath, "r") as f:
        return json.load(f)


def calculate_raw_cost(recipe, ingredients):
    """Calculate the total raw ingredient cost for one batch of a recipe.

    Args:
        recipe: A single recipe dictionary containing 'ingredients' mapping.
        ingredients: Dictionary of all available ingredients with costs.

    Returns:
        Total raw cost in INR for one batch.

    Raises:
        ValueError: If a recipe ingredient is not found in the ingredients list.
    """
    total = 0.0
    for ingredient_name, quantity in recipe["ingredients"].items():
        if ingredient_name not in ingredients:
            raise ValueError(
                f"Ingredient '{ingredient_name}' not found in ingredients list. "
                f"Please add it using the 'add-ingredient' command."
            )
        cost_per_unit = ingredients[ingredient_name]["cost_per_unit"]
        total += quantity * cost_per_unit
    return total


def calculate_unit_cost(recipe, ingredients):
    """Calculate the cost per unit (per piece/loaf/etc.) for a recipe.

    Args:
        recipe: A single recipe dictionary.
        ingredients: Dictionary of all available ingredients with costs.

    Returns:
        Cost per unit in INR.
    """
    raw_cost = calculate_raw_cost(recipe, ingredients)
    return raw_cost / recipe["batch_yield"]


def calculate_selling_price(recipe, ingredients):
    """Calculate the recommended selling price per unit.

    The selling price includes:
    - Raw ingredient cost per unit
    - Overhead costs (gas/electricity per unit, packaging, labor per unit)
    - Profit margin percentage applied on the total cost

    The final price is rounded to the nearest 5 INR for clean pricing.

    Args:
        recipe: A single recipe dictionary.
        ingredients: Dictionary of all available ingredients with costs.

    Returns:
        Selling price per unit in INR, rounded to nearest 5.
    """
    raw_cost = calculate_raw_cost(recipe, ingredients)
    batch_yield = recipe["batch_yield"]
    overhead = recipe["overhead"]
    margin_percent = recipe["profit_margin_percent"]

    # Per-unit raw cost
    unit_raw_cost = raw_cost / batch_yield

    # Per-unit overhead
    gas_electricity_per_unit = overhead["gas_electricity"] / batch_yield
    packaging_per_unit = overhead["packaging_per_unit"]
    labor_per_unit = overhead["labor_per_batch"] / batch_yield

    total_cost_per_unit = (
        unit_raw_cost + gas_electricity_per_unit + packaging_per_unit + labor_per_unit
    )

    # Apply profit margin
    selling_price = total_cost_per_unit * (1 + margin_percent / 100)

    # Round to nearest 5
    return round_to_nearest_5(selling_price)


def round_to_nearest_5(value):
    """Round a value to the nearest multiple of 5.

    Args:
        value: The number to round.

    Returns:
        The value rounded to the nearest 5.
    """
    return math.ceil(value / 5) * 5


def generate_price_list(recipes, ingredients):
    """Generate a complete price list for all recipes.

    Args:
        recipes: Dictionary of all recipes.
        ingredients: Dictionary of all available ingredients with costs.

    Returns:
        List of dictionaries, each containing:
        - name: Recipe key
        - description: Human-readable name
        - raw_cost: Total raw cost per batch
        - unit_cost: Raw cost per unit
        - selling_price: Recommended selling price per unit
        - profit_margin_percent: Configured profit margin
        - batch_yield: Number of units per batch
        - yield_unit: Unit type (pieces, loaf, etc.)
    """
    price_list = []
    for recipe_name, recipe in recipes.items():
        raw_cost = calculate_raw_cost(recipe, ingredients)
        unit_cost = calculate_unit_cost(recipe, ingredients)
        selling_price = calculate_selling_price(recipe, ingredients)
        price_list.append({
            "name": recipe_name,
            "description": recipe["description"],
            "raw_cost": round(raw_cost, 2),
            "unit_cost": round(unit_cost, 2),
            "selling_price": selling_price,
            "profit_margin_percent": recipe["profit_margin_percent"],
            "batch_yield": recipe["batch_yield"],
            "yield_unit": recipe["yield_unit"],
        })
    return price_list


def export_to_csv(price_list, output_path):
    """Export the price list to a CSV file.

    Args:
        price_list: List of dictionaries from generate_price_list().
        output_path: File path for the CSV output.
    """
    fieldnames = [
        "description",
        "raw_cost",
        "batch_yield",
        "yield_unit",
        "unit_cost",
        "selling_price",
        "profit_margin_percent",
    ]
    headers = [
        "Item",
        "Raw Cost (INR)",
        "Batch Yield",
        "Yield Unit",
        "Unit Cost (INR)",
        "Selling Price (INR)",
        "Profit Margin (%)",
    ]

    with open(output_path, "w", newline="") as f:
        writer = csv.writer(f)
        writer.writerow(headers)
        for item in price_list:
            writer.writerow([item[field] for field in fieldnames])

    return output_path
