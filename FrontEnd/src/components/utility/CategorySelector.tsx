import React from "react";

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
  return (
    <div className="categorie-wrapper">
      <label>Catégorie :</label>
      <div className="categorie-options">
        {categories.map((cat) => (
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
        ))}
      </div>
      {error && <p className="form-error">{error}</p>}
    </div>
  );
};

export default CategorySelector;
