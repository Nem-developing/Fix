// Permet de fermer les modal lors d'un clic hors de la popup ou lors d'un clic sur echap

import { useEffect, useRef, useCallback, useState } from "react";

interface UseModalCloseOptions {
  onClose: () => void;
  animationDuration?: number;
}

const useModalClose = ({
  onClose,
  animationDuration = 300,
}: UseModalCloseOptions) => {
  const modalContentRef = useRef<HTMLDivElement>(null);
  const [isClosing, setIsClosing] = useState(false);

  const handleCloseWithAnimation = useCallback(() => {
    setIsClosing(true);
    setTimeout(() => {
      onClose();
    }, animationDuration);
  }, [onClose, animationDuration]);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        modalContentRef.current &&
        !modalContentRef.current.contains(event.target as Node)
      ) {
        handleCloseWithAnimation();
      }
    };

    const handleKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        handleCloseWithAnimation();
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    document.addEventListener("keydown", handleKeyDown);

    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
      document.removeEventListener("keydown", handleKeyDown);
    };
  }, [handleCloseWithAnimation]);

  return { modalContentRef, isClosing, handleCloseWithAnimation };
};

export default useModalClose;
