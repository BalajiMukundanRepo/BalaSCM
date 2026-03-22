"""Command-line interface for the baking cost calculator."""

import argparse
import json
import os
import sys

from baking_calculator.calculator import (
    load_ingredients,
    load_recipes,
    calculate_raw_cost,
    calculate_unit_cost,
    calculate_selling_price,
    generate_price_list,
    export_to_csv,
)

# Paths to data files (relative to the package directory)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
INGREDIENTS_FILE = os.path.join(BASE_DIR, "ingredients.json")
RECIPES_FILE = os.path.join(BASE_DIR, "recipes.json")


def format_inr(value):
    """Format a numeric value as INR currency string."""
    return f"\u20b9{value:.2f}"


def print_table(headers, rows, col_widths=None):
    """Print a formatted table with box-drawing characters.

    Args:
        headers: List of header strings.
        rows: List of lists, each inner list is a row of values.
        col_widths: Optional list of column widths. Auto-calculated if None.
    """
    if col_widths is None:
        col_widths = []
        for i, header in enumerate(headers):
            max_width = len(header)
            for row in rows:
                max_width = max(max_width, len(str(row[i])))
            col_widths.append(max_width + 2)

    # Top border
    top = "\u2554" + "\u2566".join("\u2550" * w for w in col_widths) + "\u2557"
    # Header separator
    mid = "\u2560" + "\u256c".join("\u2550" * w for w in col_widths) + "\u2563"
    # Bottom border
    bot = "\u255a" + "\u2569".join("\u2550" * w for w in col_widths) + "\u255d"

    print(top)
    # Header row
    header_cells = []
    for i, header in enumerate(headers):
        header_cells.append(" " + header.ljust(col_widths[i] - 1))
    print("\u2551" + "\u2551".join(header_cells) + "\u2551")
    print(mid)

    # Data rows
    for row in rows:
        cells = []
        for i, val in enumerate(row):
            cells.append(" " + str(val).ljust(col_widths[i] - 1))
        print("\u2551" + "\u2551".join(cells) + "\u2551")

    print(bot)


def cmd_pricelist(args):
    """Show the full catalogue with selling prices."""
    ingredients = load_ingredients(INGREDIENTS_FILE)
    recipes = load_recipes(RECIPES_FILE)
    price_list = generate_price_list(recipes, ingredients)

    headers = ["Item", "Raw Cost", "Unit Cost", "Selling Price", "Margin"]
    rows = []
    for item in price_list:
        rows.append([
            item["description"],
            format_inr(item["raw_cost"]),
            format_inr(item["unit_cost"]),
            format_inr(item["selling_price"]),
            f"{item['profit_margin_percent']}%",
        ])

    print("\n  Baking Cost Calculator - Price List")
    print("  " + "=" * 40)
    print()
    print_table(headers, rows)
    print(f"\n  All prices are in INR (\u20b9). Selling prices rounded to nearest \u20b95.")
    print()


def cmd_cost(args):
    """Show detailed cost breakdown for a specific recipe."""
    ingredients = load_ingredients(INGREDIENTS_FILE)
    recipes = load_recipes(RECIPES_FILE)

    recipe_name = args.recipe_name
    if recipe_name not in recipes:
        print(f"\nError: Recipe '{recipe_name}' not found.")
        print("Available recipes:")
        for name, recipe in recipes.items():
            print(f"  - {name} ({recipe['description']})")
        sys.exit(1)

    recipe = recipes[recipe_name]
    print(f"\n  Cost Breakdown: {recipe['description']}")
    print("  " + "=" * 50)
    print(f"  Batch Yield: {recipe['batch_yield']} {recipe['yield_unit']}")
    print()

    # Ingredient breakdown
    headers = ["Ingredient", "Qty", "Unit", "Rate", "Cost"]
    rows = []
    total_raw = 0.0
    for ing_name, qty in recipe["ingredients"].items():
        if ing_name not in ingredients:
            print(f"  WARNING: Ingredient '{ing_name}' not found!")
            continue
        ing = ingredients[ing_name]
        cost = qty * ing["cost_per_unit"]
        total_raw += cost
        display_name = ing_name.replace("_", " ").title()
        rows.append([
            display_name,
            f"{qty:g}",
            ing["unit"],
            format_inr(ing["cost_per_unit"]),
            format_inr(cost),
        ])

    print("  Ingredients:")
    print_table(headers, rows)

    # Overhead breakdown
    overhead = recipe["overhead"]
    batch_yield = recipe["batch_yield"]
    gas_elec = overhead["gas_electricity"]
    packaging = overhead["packaging_per_unit"] * batch_yield
    labor = overhead["labor_per_batch"]
    total_overhead = gas_elec + packaging + labor

    print(f"\n  Overhead Costs (per batch):")
    print(f"    Gas/Electricity:  {format_inr(gas_elec)}")
    print(f"    Packaging:        {format_inr(packaging)} ({format_inr(overhead['packaging_per_unit'])} x {batch_yield})")
    print(f"    Labor:            {format_inr(labor)}")
    print(f"    Total Overhead:   {format_inr(total_overhead)}")

    # Summary
    total_batch_cost = total_raw + total_overhead
    unit_cost_val = total_batch_cost / batch_yield
    margin = recipe["profit_margin_percent"]
    selling_price = calculate_selling_price(recipe, ingredients)

    print(f"\n  Summary:")
    print(f"    Raw Material Cost (batch):  {format_inr(total_raw)}")
    print(f"    Total Overhead (batch):     {format_inr(total_overhead)}")
    print(f"    Total Batch Cost:           {format_inr(total_batch_cost)}")
    print(f"    Cost Per Unit:              {format_inr(unit_cost_val)}")
    print(f"    Profit Margin:              {margin}%")
    print(f"    Selling Price Per Unit:     {format_inr(selling_price)}")
    print()


def cmd_add_ingredient(args):
    """Interactively add a new ingredient to ingredients.json."""
    ingredients = load_ingredients(INGREDIENTS_FILE)

    print("\n  Add New Ingredient")
    print("  " + "=" * 30)

    name = input("  Ingredient name (e.g., almond_flour): ").strip().lower().replace(" ", "_")
    if not name:
        print("  Error: Name cannot be empty.")
        sys.exit(1)

    if name in ingredients:
        print(f"  Warning: '{name}' already exists with cost {format_inr(ingredients[name]['cost_per_unit'])} per {ingredients[name]['unit']}.")
        overwrite = input("  Overwrite? (y/n): ").strip().lower()
        if overwrite != "y":
            print("  Cancelled.")
            return

    unit = input("  Unit (kg/litre/piece/ml): ").strip().lower()
    if unit not in ("kg", "litre", "piece", "ml"):
        print("  Warning: Non-standard unit. Proceeding anyway.")

    try:
        cost = float(input(f"  Cost per {unit} in INR: ").strip())
    except ValueError:
        print("  Error: Invalid cost value.")
        sys.exit(1)

    ingredients[name] = {"unit": unit, "cost_per_unit": cost}

    with open(INGREDIENTS_FILE, "w") as f:
        json.dump(ingredients, f, indent=2)

    print(f"\n  Added '{name}' at {format_inr(cost)} per {unit}.")
    print()


def cmd_add_recipe(args):
    """Interactively add a new recipe to recipes.json."""
    ingredients = load_ingredients(INGREDIENTS_FILE)
    recipes = load_recipes(RECIPES_FILE)

    print("\n  Add New Recipe")
    print("  " + "=" * 30)

    name = input("  Recipe key (e.g., banana_bread): ").strip().lower().replace(" ", "_")
    if not name:
        print("  Error: Name cannot be empty.")
        sys.exit(1)

    if name in recipes:
        print(f"  Warning: Recipe '{name}' already exists.")
        overwrite = input("  Overwrite? (y/n): ").strip().lower()
        if overwrite != "y":
            print("  Cancelled.")
            return

    description = input("  Description (e.g., Classic Banana Bread): ").strip()

    try:
        batch_yield = int(input("  Batch yield (number of units per batch): ").strip())
    except ValueError:
        print("  Error: Invalid yield value.")
        sys.exit(1)

    yield_unit = input("  Yield unit (pieces/loaf/slices): ").strip()

    # Add ingredients
    recipe_ingredients = {}
    print("\n  Add ingredients (type 'done' when finished):")
    print("  Available ingredients:", ", ".join(sorted(ingredients.keys())))
    print()

    while True:
        ing_name = input("  Ingredient name (or 'done'): ").strip().lower().replace(" ", "_")
        if ing_name == "done":
            break
        if ing_name not in ingredients:
            print(f"  Warning: '{ing_name}' not in ingredients list. Add it first with 'add-ingredient'.")
            continue
        try:
            qty = float(input(f"  Quantity of {ing_name} ({ingredients[ing_name]['unit']}): ").strip())
        except ValueError:
            print("  Error: Invalid quantity.")
            continue
        recipe_ingredients[ing_name] = qty

    if not recipe_ingredients:
        print("  Error: Recipe must have at least one ingredient.")
        sys.exit(1)

    # Overhead costs
    print("\n  Overhead costs:")
    try:
        gas = float(input("  Gas/Electricity per batch (INR): ").strip())
        packaging = float(input("  Packaging cost per unit (INR): ").strip())
        labor = float(input("  Labor cost per batch (INR): ").strip())
    except ValueError:
        print("  Error: Invalid cost value.")
        sys.exit(1)

    try:
        margin = float(input("  Profit margin percentage: ").strip())
    except ValueError:
        print("  Error: Invalid margin value.")
        sys.exit(1)

    recipe = {
        "description": description,
        "batch_yield": batch_yield,
        "yield_unit": yield_unit,
        "ingredients": recipe_ingredients,
        "overhead": {
            "gas_electricity": gas,
            "packaging_per_unit": packaging,
            "labor_per_batch": labor,
        },
        "profit_margin_percent": margin,
    }

    recipes[name] = recipe

    with open(RECIPES_FILE, "w") as f:
        json.dump(recipes, f, indent=2)

    print(f"\n  Recipe '{name}' added successfully!")
    print()


def cmd_update_price(args):
    """Update an ingredient's cost per unit."""
    ingredients = load_ingredients(INGREDIENTS_FILE)

    ingredient_name = args.ingredient
    if ingredient_name not in ingredients:
        print(f"\nError: Ingredient '{ingredient_name}' not found.")
        print("Available ingredients:")
        for name in sorted(ingredients.keys()):
            ing = ingredients[name]
            print(f"  - {name} ({format_inr(ing['cost_per_unit'])} per {ing['unit']})")
        sys.exit(1)

    old_price = ingredients[ingredient_name]["cost_per_unit"]
    new_price = args.new_price

    ingredients[ingredient_name]["cost_per_unit"] = new_price

    with open(INGREDIENTS_FILE, "w") as f:
        json.dump(ingredients, f, indent=2)

    unit = ingredients[ingredient_name]["unit"]
    display_name = ingredient_name.replace("_", " ").title()
    print(f"\n  Updated '{display_name}' price:")
    print(f"    Old: {format_inr(old_price)} per {unit}")
    print(f"    New: {format_inr(new_price)} per {unit}")
    print()


def cmd_export(args):
    """Export the full price list to a CSV file."""
    ingredients = load_ingredients(INGREDIENTS_FILE)
    recipes = load_recipes(RECIPES_FILE)
    price_list = generate_price_list(recipes, ingredients)

    output_path = args.output
    export_to_csv(price_list, output_path)
    print(f"\n  Price list exported to: {output_path}")
    print(f"  Total items: {len(price_list)}")
    print()


def main():
    """Main entry point for the CLI."""
    parser = argparse.ArgumentParser(
        prog="baking_calculator",
        description="Baking Cost Calculator - Calculate ingredient costs and selling prices for baked goods.",
    )
    subparsers = parser.add_subparsers(dest="command", help="Available commands")

    # pricelist command
    subparsers.add_parser(
        "pricelist",
        help="Show the full catalogue with selling prices",
    )

    # cost command
    cost_parser = subparsers.add_parser(
        "cost",
        help="Show detailed cost breakdown for a specific recipe",
    )
    cost_parser.add_argument(
        "recipe_name",
        help="Name of the recipe (e.g., chocolate_brownies)",
    )

    # add-ingredient command
    subparsers.add_parser(
        "add-ingredient",
        help="Interactively add a new ingredient",
    )

    # add-recipe command
    subparsers.add_parser(
        "add-recipe",
        help="Interactively add a new recipe",
    )

    # update-price command
    update_parser = subparsers.add_parser(
        "update-price",
        help="Update an ingredient's price",
    )
    update_parser.add_argument(
        "ingredient",
        help="Ingredient name (e.g., butter)",
    )
    update_parser.add_argument(
        "new_price",
        type=float,
        help="New price per unit in INR",
    )

    # export command
    export_parser = subparsers.add_parser(
        "export",
        help="Export the full price list to a CSV file",
    )
    export_parser.add_argument(
        "-o", "--output",
        default="price_list.csv",
        help="Output CSV file path (default: price_list.csv)",
    )

    args = parser.parse_args()

    if args.command is None:
        parser.print_help()
        sys.exit(0)

    command_map = {
        "pricelist": cmd_pricelist,
        "cost": cmd_cost,
        "add-ingredient": cmd_add_ingredient,
        "add-recipe": cmd_add_recipe,
        "update-price": cmd_update_price,
        "export": cmd_export,
    }

    command_map[args.command](args)


if __name__ == "__main__":
    main()
