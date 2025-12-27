import React from "react";
import { useNavigate } from "react-router-dom";
import { createTicket } from "../services/apiService";
import InputField from "../components/utility/InputField";
import CategorySelector from "../components/utility/CategorySelector";
import UrgencySelector from "../components/utility/UrgencySelector";
import useForm from "../hooks/useForm";
import { useSuccessContext } from "../context/SuccessContext";
import { categories } from "../constants/categories";

interface NewTicketFormValues {
  titre: string;
  categorie: string;
  description: string;
  urgence: number;
}

const NewTicket: React.FC = () => {
  const navigate = useNavigate();
  const { triggerSuccess } = useSuccessContext();

  const initialFormValues: NewTicketFormValues = {
    titre: "",
    categorie: "",
    description: "",
    urgence: 0,
  };

  const {
    values,
    handleChange,
    handleManualChange,
    handleSubmit,
    errors,
    setErrors,
  } = useForm<NewTicketFormValues>({
    initialValues: initialFormValues,
    onSubmit: async (data) => {
      try {
        const result = await createTicket(data);
        console.log("Ticket créé :", result);
        triggerSuccess();
        navigate("/dashboard");
      } catch (error) {
        console.error("Erreur création ticket :", error);
        alert("Erreur lors de la création du ticket.");
      }
    },
  });

  const validateForm = (data: NewTicketFormValues) => {
    const newErrors: Partial<NewTicketFormValues> = {};
    if (
      !data.categorie ||
      (!categories.includes(data.categorie) && data.categorie.trim().length < 1)
    ) {
      newErrors.categorie =
        "Veuillez sélectionner ou saisir une catégorie valide.";
    }
    return newErrors;
  };

  return (
    <div className="NewTicket">
      <h1>Nouveau Ticket</h1>
      <div className="formdiv">
        <form onSubmit={(e) => handleSubmit(e, validateForm)}>
          <InputField
            id="titre"
            name="titre"
            label="Titre"
            type="text"
            value={values.titre}
            onChange={handleChange}
            required
          />

          <InputField
            id="description"
            name="description"
            label="Description"
            type="textarea"
            value={values.description}
            onChange={handleChange}
            required
          />

          <CategorySelector
            selectedCategory={values.categorie}
            onSelectCategory={(cat) => handleManualChange("categorie", cat)}
            error={errors.categorie}
          />

          <UrgencySelector
            selectedUrgency={values.urgence}
            onSelectUrgency={(urg) => handleManualChange("urgence", urg)}
          />

          <button type="submit">Créer le ticket</button>
        </form>
      </div>
    </div>
  );
};

export default NewTicket;
