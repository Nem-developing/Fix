import { useState, useCallback } from "react";

interface UseFormOptions<T> {
  initialValues: T;
  onSubmit: (values: T) => void;
}

const useForm = <T extends Record<string, any>>({
  initialValues,
  onSubmit,
}: UseFormOptions<T>) => {
  const [values, setValues] = useState<T>(initialValues);
  const [errors, setErrors] = useState<Partial<T>>({});

  const handleChange = useCallback(
    (
      e: React.ChangeEvent<
        HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
      >
    ) => {
      const { name, value, type } = e.target;
      setValues((prevValues) => ({
        ...prevValues,
        [name]:
          type === "number" || name === "urgence" ? parseInt(value, 10) : value,
      }));
      if (errors[name as keyof T]) {
        setErrors((prevErrors) => ({ ...prevErrors, [name]: undefined }));
      }
    },
    [errors]
  );

  const handleManualChange = useCallback(
    (name: keyof T, value: T[keyof T]) => {
      setValues((prevValues) => ({
        ...prevValues,
        [name]: value,
      }));
      if (errors[name]) {
        setErrors((prevErrors) => ({ ...prevErrors, [name]: undefined }));
      }
    },
    [errors]
  );

  const handleSubmit = useCallback(
    async (e: React.FormEvent, validationFn?: (values: T) => Partial<T>) => {
      e.preventDefault();
      let currentErrors: Partial<T> = {};

      if (validationFn) {
        currentErrors = validationFn(values);
        setErrors(currentErrors);
      }

      if (Object.keys(currentErrors).length === 0) {
        await onSubmit(values);
      }
    },
    [values, onSubmit]
  );

  return {
    values,
    handleChange,
    handleManualChange,
    handleSubmit,
    setValues,
    errors,
    setErrors,
  };
};

export default useForm;
