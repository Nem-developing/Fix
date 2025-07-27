import React from "react";
import { useNavigate } from "react-router-dom";
import { createTicket } from "../services/apiService";
import ConfettiExplosion from "../components/style/ConfettiExplosion";
import InputField from "../components/utility/InputField";
import CategorySelector from "../components/utility/CategorySelector";
import UrgencySelector from "../components/utility/UrgencySelector";
import useForm from "../hooks/useForm";
import useSuccessAnimation from "../hooks/useSuccessAnimation";

interface NewTicketFormValues {
  titre: string;
  categorie: string;
  description: string;
  urgence: number;
}

const NewTicket: React.FC = () => {
  const navigate = useNavigate();
  const { showSuccess, successMessageRef, confettiY, triggerSuccess } =
    useSuccessAnimation();

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
        setTimeout(() => {
          navigate("/dashboard");
        }, 2500);
      } catch (error) {
        console.error("Erreur création ticket :", error);
        alert("Erreur lors de la création du ticket.");
      }
    },
  });

  const validateForm = (data: NewTicketFormValues) => {
    const newErrors: Partial<NewTicketFormValues> = {};
    if (!data.categorie) {
      newErrors.categorie = "Veuillez sélectionner une catégorie.";
    }
    return newErrors;
  };

  return (
    <div className="NewTicket">
      {showSuccess && (
        <>
          <div className="success-overlay-background"></div>
          <ConfettiExplosion trigger={showSuccess} yOffset={confettiY} />
          <div className="success-overlay-content">
            <h2 ref={successMessageRef}>🎉 Ticket créé !</h2>
          </div>
        </>
      )}

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
