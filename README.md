# Baking Cost Calculator

A Python-based command-line tool to calculate ingredient costs, overhead, and selling prices for baked goods. Built for home bakers and small bakery businesses in India.

## Features

- **Price List**: View a formatted table of all items with raw costs, unit costs, and selling prices
- **Cost Breakdown**: Get detailed ingredient-by-ingredient cost analysis for any recipe
- **Ingredient Management**: Add new ingredients or update prices when market rates change
- **Recipe Management**: Add new recipes interactively with ingredient quantities and overhead costs
- **CSV Export**: Export the full price list to a CSV file for use in spreadsheets
- **Indian Market Prices**: Pre-configured with realistic INR prices for common baking ingredients
- **Zero Dependencies**: Uses only Python standard library (json, csv, argparse, os)

## Quick Start

```bash
# View the full price list
python -m baking_calculator pricelist

# View cost breakdown for a specific recipe
python -m baking_calculator cost chocolate_brownies

# Export prices to CSV
python -m baking_calculator export
```

## Available Commands

### `pricelist` — Show Full Catalogue

```bash
python -m baking_calculator pricelist
```

Displays a formatted table with all items, their raw costs, unit costs, and recommended selling prices:

```
╔══════════════════════════╦══════════════╦═══════════╦═══════════════╦════════╗
║ Item                     ║ Raw Cost     ║ Unit Cost ║ Selling Price ║ Margin ║
╠══════════════════════════╬══════════════╬═══════════╬═══════════════╬════════╣
║ Classic Chocolate Brownies║ ₹185.50     ║ ₹15.46   ║ ₹35.00        ║ 40%   ║
║ Soft Vanilla Muffins     ║ ₹92.45      ║ ₹7.70    ║ ₹20.00        ║ 45%   ║
║ ...                      ║ ...          ║ ...       ║ ...           ║ ...    ║
╚══════════════════════════╩══════════════╩═══════════╩═══════════════╩════════╝
```

### `cost <recipe_name>` — Detailed Cost Breakdown

```bash
python -m baking_calculator cost chocolate_brownies
```

Shows a line-by-line ingredient breakdown, overhead costs, and final pricing for the specified recipe.

### `add-ingredient` — Add New Ingredient

```bash
python -m baking_calculator add-ingredient
```

Interactively prompts for:
- Ingredient name (e.g., `almond_flour`)
- Unit type (kg, litre, piece, ml)
- Cost per unit in INR

### `add-recipe` — Add New Recipe

```bash
python -m baking_calculator add-recipe
```

Interactively prompts for recipe details including ingredients, quantities, overhead costs, and profit margin.

### `update-price <ingredient> <new_price>` — Update Ingredient Price

```bash
# Update butter price to ₹550 per kg
python -m baking_calculator update-price butter 550
```

Useful when market prices change. Updates the ingredient cost and recalculates all affected recipes.

### `export` — Export to CSV

```bash
# Export to default file (price_list.csv)
python -m baking_calculator export

# Export to a custom file
python -m baking_calculator export -o my_prices.csv
```

## Pre-loaded Recipes

The calculator comes pre-loaded with these recipes:

| Recipe | Yield | Description |
|--------|-------|-------------|
| `chocolate_brownies` | 12 pieces | Classic Chocolate Brownies |
| `vanilla_muffins` | 12 pieces | Soft Vanilla Muffins |
| `butter_cookies` | 30 pieces | Crispy Butter Cookies |
| `margherita_pizza` | 4 pieces | Classic Margherita Pizza |
| `ragi_millet_bread` | 1 loaf | Healthy Ragi Millet Bread |

## Pre-loaded Ingredients

The calculator includes 25 common baking ingredients with Indian market prices, including:
- Basic: flour, sugar, butter, eggs, milk, salt
- Baking: baking powder, baking soda, yeast, vanilla extract
- Specialty: cocoa powder, chocolate chips, cream cheese, mozzarella
- Millet flours: ragi, jowar, bajra
- Others: olive oil, honey, condensed milk, cinnamon, pizza sauce, packaging

## How Pricing Works

1. **Raw Cost**: Sum of (quantity x cost_per_unit) for all ingredients in a batch
2. **Unit Cost**: Raw cost divided by batch yield
3. **Overhead**: Gas/electricity + packaging + labor costs allocated per unit
4. **Selling Price**: (Unit cost + overhead) x (1 + profit margin%) rounded to nearest ₹5

## Running Tests

```bash
python -m unittest discover tests -v
```

## Project Structure

```
baking_calculator/
├── __init__.py          # Package init
├── __main__.py          # Entry point for python -m
├── calculator.py        # Core pricing engine
├── cli.py               # Command-line interface
├── ingredients.json     # Ingredient database
└── recipes.json         # Recipe database
tests/
└── test_calculator.py   # Unit tests
README.md
```

## All Prices in INR

This calculator is designed for the Indian market. All ingredient costs and selling prices are in Indian Rupees (INR).
