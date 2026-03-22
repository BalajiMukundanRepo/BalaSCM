# Testing the Baking Cost Calculator CLI

## Overview
The baking calculator is a pure-Python CLI app (`python -m baking_calculator`) with no external dependencies. It calculates ingredient costs, overhead, and selling prices for baked goods.

## Prerequisites
- Python 3.x (standard library only, no pip install needed)
- Run all commands from the repo root directory

## Devin Secrets Needed
None — this is a fully offline CLI tool.

## How to Run
```bash
cd /home/ubuntu/repos/BalaSCM
python -m baking_calculator <subcommand>
```

## Available Subcommands
| Command | Description | Interactive? |
|---------|-------------|--------------|
| `pricelist` | Show formatted price table | No |
| `cost <recipe_name>` | Detailed cost breakdown | No |
| `add-ingredient` | Add new ingredient | Yes (stdin prompts) |
| `add-recipe` | Add new recipe | Yes (stdin prompts) |
| `update-price <ingredient> <price>` | Update ingredient price | No |
| `export -o <file>` | Export to CSV | No |

## Testing Strategy

### 1. Visual CLI Testing (use konsole)
Since this is a CLI app with formatted table output (box-drawing characters), test in a GUI terminal (konsole) to visually verify the table rendering.

### 2. Key Test Sequence
```bash
# Test price list display
python -m baking_calculator pricelist

# Test detailed cost breakdown
python -m baking_calculator cost chocolate_brownies

# Test error handling for invalid recipe
python -m baking_calculator cost nonexistent_recipe

# Test price update and propagation
python -m baking_calculator update-price butter 550
python -m baking_calculator pricelist  # verify values changed
python -m baking_calculator update-price butter 500  # restore original

# Test CSV export
python -m baking_calculator export -o /tmp/test_prices.csv
cat /tmp/test_prices.csv

# Test help output
python -m baking_calculator --help

# Run unit tests
python -m unittest discover tests -v
```

### 3. Verifying Computed Values
To verify correctness, manually compute expected values from `ingredients.json` and `recipes.json`. For chocolate_brownies with default prices:
- Raw cost: ₹220.25 (sum of qty × cost_per_unit for each ingredient)
- Unit cost: ₹18.35 (raw_cost / batch_yield of 12)
- Selling price: ₹60.00 (total_cost_per_unit × 1.4, rounded up to nearest ₹5)

### 4. State Management
- `update-price` and `add-ingredient`/`add-recipe` modify JSON files in-place inside the `baking_calculator/` package directory
- Always restore original values after testing price updates (e.g., reset butter to 500)
- If JSON files get corrupted during testing, use `git checkout -- baking_calculator/ingredients.json baking_calculator/recipes.json` to restore

### 5. Interactive Commands
The `add-ingredient` and `add-recipe` commands require stdin input. These are harder to test automatically. Consider testing them manually in konsole or using echo piping:
```bash
echo -e "test_ingredient\nkg\n100" | python -m baking_calculator add-ingredient
```
Note: This may not work perfectly since the prompts use `input()`. Manual testing in konsole is more reliable for these.

## Common Issues
- The `round_to_nearest_5` function uses `math.ceil`, so it always rounds UP (e.g., ₹21 → ₹25, not ₹20)
- No guard against `batch_yield=0` — would cause ZeroDivisionError if someone manually edits JSON
- The CSV export path is relative to the current working directory, not the package directory
