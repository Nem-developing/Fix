import { useState, useRef, useEffect, useCallback } from "react";
import { useWindowSize } from "@react-hook/window-size";

interface UseSuccessAnimationResult {
  showSuccess: boolean;
  successMessageRef: React.RefObject<HTMLHeadingElement | null>;
  confettiY: number;
  triggerSuccess: () => void;
}

const useSuccessAnimation = (): UseSuccessAnimationResult => {
  const [showSuccess, setShowSuccess] = useState(false);
  const [confettiY, setConfettiY] = useState(0.5);
  const successMessageRef = useRef<HTMLHeadingElement>(null);
  const [width, height] = useWindowSize();

  const triggerSuccess = useCallback(() => {
    setShowSuccess(true);
    setTimeout(() => {
      if (successMessageRef.current) {
        const rect = successMessageRef.current.getBoundingClientRect();
        setConfettiY(rect.bottom / height + 0.02);
      }
    }, 50);

    setTimeout(() => {
      setShowSuccess(false);
    }, 2500);
  }, [height]);

  return {
    showSuccess,
    successMessageRef,
    confettiY,
    triggerSuccess,
  };
};

export default useSuccessAnimation;
