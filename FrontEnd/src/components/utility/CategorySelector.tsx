import React, { useState, useEffect } from "react";

interface CategorySelectorProps {
  selectedCategory: string;
  onSelectCategory: (category: string) => void;
  error?: string;
}

const categories = [
  "Support technique",
  "Demande d'information",
  "Problème de compte",
  "Suggestion",
  "Bug",
  "Autre",
];

const CategorySelector: React.FC<CategorySelectorProps> = ({
  selectedCategory,
  onSelectCategory,
  error,
}) => {
  const [customCategory, setCustomCategory] = useState("");

  const isCustom =
    selectedCategory !== "" &&
    !categories.includes(selectedCategory) &&
    customCategory.trim().length > 0;

  useEffect(() => {
    if (!isCustom) {
      setCustomCategory("");
    }
  }, [selectedCategory]);

  const handleCustomCategoryChange = (
    e: React.ChangeEvent<HTMLInputElement>
  ) => {
    const value = e.target.value;
    setCustomCategory(value);

    if (value.trim().length > 0) {
      onSelectCategory(value);
    } else {
      onSelectCategory("");
    }
  };

  return (
    <div className="categorie-wrapper">
      <label>Catégorie :</label>
      <div className="categorie-options">
        {categories.map((cat) =>
          cat === "Autre" ? (
            <input
              key="custom"
              type="text"
              placeholder="Autre..."
              className={`categorie-button ${isCustom ? "selected" : ""}`}
              value={customCategory}
              maxLength={30}
              onChange={handleCustomCategoryChange}
            />
          ) : (
            <button
              type="button"
              key={cat}
              className={`categorie-button ${
                selectedCategory === cat ? "selected" : ""
              }`}
              onClick={() => onSelectCategory(cat)}
            >
              {cat}
            </button>
          )
        )}
      </div>
      {error && <p className="form-error">{error}</p>}
    </div>
  );
};

export default CategorySelector;
