<?php if ($errors): ?>
	<div class="alert alert-error">
		<ul>
			<?php foreach ($errors as $error): ?>
				<li><?= e($error) ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php
$formIngredients = ($currentIngredients ?? []) ?: [
	[
		'quantity' => '',
		'unit' => '',
		'ingredient_name' => '',
	],
];

$selectedCategory = (int) ($_POST['category_id'] ?? ($recipe['category_id'] ?? 0));
$categoryUnits = [];

foreach ($categories as $category) {
	$units = json_decode((string) ($category['unit_options'] ?? '[]'), true);
	$categoryUnits[(int) $category['id']] = is_array($units) ? $units : [];
}
?>

<form class="recipe-form" method="post" novalidate>
	<?= csrfField() ?>

	<div class="form-two-col">
		<label>
			Recipe title
			<input
				type="text"
				name="title"
				value="<?= old('title', $recipe['title'] ?? '') ?>"
				required
				maxlength="150"
			>
		</label>

		<label>
			Category
			<select id="category-select" name="category_id" required>
				<option value="">Choose one</option>
				<?php foreach ($categories as $category): ?>
					<option
						value="<?= (int) $category['id'] ?>"
						data-unit-options="<?= e($category['unit_options'] ?? '[]') ?>"
						<?= $selectedCategory === (int) $category['id'] ? 'selected' : '' ?>
					>
						<?= e($category['name']) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>

	<label>
		Short description
		<textarea
			name="short_description"
			rows="2"
			maxlength="255"
			required
		><?= old('short_description', $recipe['short_description'] ?? '') ?></textarea>
	</label>

	<fieldset>
		<legend>Ingredients</legend>
		<p class="field-hint">
			Add the quantity, unit, and ingredient name for each line.
		</p>

		<div id="ingredient-list">
			<?php foreach ($formIngredients as $ingredient): ?>
				<?php
				$currentUnit = (string) ($ingredient['unit'] ?? '');
				$availableUnits = $categoryUnits[$selectedCategory] ?? [];
				if ($currentUnit !== '' && !in_array($currentUnit, $availableUnits, true)) {
					$availableUnits[] = $currentUnit;
				}
				?>
				<div class="ingredient-row">
					<input
						type="text"
						name="quantity[]"
						placeholder="1/2"
						value="<?= e($ingredient['quantity'] ?? '') ?>"
						aria-label="Quantity"
						required
					>
					<div class="unit-control">
						<select class="unit-select" name="unit[]" aria-label="Unit" required>
							<option value="">Unit</option>
							<?php foreach ($availableUnits as $unit): ?>
								<option value="<?= e((string) $unit) ?>" <?= $currentUnit === $unit ? 'selected' : '' ?>>
									<?= e((string) $unit) ?>
								</option>
							<?php endforeach; ?>
							<option value="__add_custom_unit__">+ Add custom unit</option>
						</select>
					</div>
					<input
						class="ingredient-name"
						type="text"
						name="ingredient_name[]"
						placeholder="All-purpose flour"
						value="<?= e($ingredient['ingredient_name'] ?? $ingredient['name'] ?? '') ?>"
						aria-label="Ingredient name"
						required
					>
					<button
						class="icon-button remove-ingredient"
						type="button"
						aria-label="Remove ingredient"
					>
						&times;
					</button>
				</div>
			<?php endforeach; ?>
		</div>

		<button class="outline-button" id="add-ingredient" type="button">
			+ Add ingredient
		</button>
	</fieldset>

	<label>
		Cooking instructions
		<textarea name="instructions" rows="8" required><?= old('instructions', $recipe['instructions'] ?? '') ?></textarea>
	</label>

	<div class="form-actions">
		<button class="button" type="submit">Save recipe</button>
		<a class="text-link" href="<?= BASE_URL ?>/recipes.php">Cancel</a>
	</div>
</form>

<div class="unit-modal" id="unit-modal" hidden>
	<div class="unit-modal-backdrop" data-close-unit-modal></div>
	<div
		class="unit-modal-card"
		role="dialog"
		aria-modal="true"
		aria-labelledby="unit-modal-title"
	>
		<button class="unit-modal-close" type="button" data-close-unit-modal aria-label="Close">
			&times;
		</button>
		<p class="eyebrow">Ingredient measurement</p>
		<h2 id="unit-modal-title">Add a custom unit</h2>
		<p class="muted">Create a measurement for this ingredient, such as grams or a handful.</p>
		<form id="unit-form">
			<label for="custom-unit">Unit or metric</label>
			<input id="custom-unit" type="text" maxlength="50" required placeholder="Example: grams">
			<div class="form-actions">
				<button class="button" type="submit">Add unit</button>
				<button class="text-link" type="button" data-close-unit-modal>Cancel</button>
			</div>
		</form>
	</div>
</div>
