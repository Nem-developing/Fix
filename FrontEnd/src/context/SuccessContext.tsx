import React, {
  createContext,
  useContext,
  useState,
  useRef,
  useCallback,
  ReactNode,
} from "react";
import ConfettiExplosion from "../components/style/ConfettiExplosion";
import { useWindowSize } from "@react-hook/window-size";

interface SuccessContextType {
  triggerSuccess: () => void;
}

const SuccessContext = createContext<SuccessContextType | undefined>(undefined);

export const SuccessProvider: React.FC<{ children: ReactNode }> = ({
  children,
}) => {
  const [showSuccess, setShowSuccess] = useState(false);
  const [confettiY, setConfettiY] = useState(0.5);
  const successMessageRef = useRef<HTMLHeadingElement>(null);
  const [, height] = useWindowSize();

  const triggerSuccess = useCallback(() => {
    setShowSuccess(true);

    // Calculate position slightly after render
    setTimeout(() => {
      if (successMessageRef.current) {
        const rect = successMessageRef.current.getBoundingClientRect();
        // Fallback to center if height is 0 (shouldn't happen)
        setConfettiY(height > 0 ? rect.bottom / height + 0.02 : 0.5);
      }
    }, 50);

    setTimeout(() => {
      setShowSuccess(false);
    }, 2500);
  }, [height]);

  return (
    <SuccessContext.Provider value={{ triggerSuccess }}>
      {children}
      {showSuccess && (
        <>
          <div className="success-overlay-background"></div>
          <ConfettiExplosion trigger={showSuccess} yOffset={confettiY} />
          <div className="success-overlay-content">
            <h2 ref={successMessageRef}>🎉 Ticket créé !</h2>
          </div>
        </>
      )}
    </SuccessContext.Provider>
  );
};

export const useSuccessContext = () => {
  const context = useContext(SuccessContext);
  if (!context) {
    throw new Error("useSuccessContext must be used within a SuccessProvider");
  }
  return context;
};
