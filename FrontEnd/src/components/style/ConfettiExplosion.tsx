import React, { useCallback, useRef, useEffect } from "react";
import ReactCanvasConfetti from "react-canvas-confetti";

const makeShot = (
  particleRatio: number,
  opts: any,
  confettiInstance: any,
  yOffset: number
) => {
  confettiInstance?.({
    origin: { x: 0.5, y: yOffset },
    particleCount: Math.floor(500 * particleRatio),
    ...opts,
  });
};

const fire = (confettiInstance: any, yOffset: number) => {
  makeShot(
    0.25,
    {
      spread: 26,
      startVelocity: 55,
    },
    confettiInstance,
    yOffset
  );
  makeShot(
    0.2,
    {
      spread: 60,
    },
    confettiInstance,
    yOffset
  );
  makeShot(
    0.35,
    {
      spread: 100,
      decay: 0.91,
      scalar: 0.8,
    },
    confettiInstance,
    yOffset
  );
  makeShot(
    0.1,
    {
      spread: 120,
      startVelocity: 25,
      decay: 0.92,
      scalar: 1.2,
    },
    confettiInstance,
    yOffset
  );
  makeShot(
    0.1,
    {
      spread: 120,
      startVelocity: 45,
    },
    confettiInstance,
    yOffset
  );
};

type Props = {
  trigger: boolean;
  yOffset?: number;
};

const ConfettiExplosion: React.FC<Props> = ({ trigger, yOffset = 0.5 }) => {
  const confettiInstanceRef = useRef<any>(null);
  const onInitConfetti = useCallback(({ confetti }: { confetti: any }) => {
    confettiInstanceRef.current = confetti;
  }, []);

  useEffect(() => {
    if (trigger && confettiInstanceRef.current) {
      fire(confettiInstanceRef.current, yOffset);
    }
  }, [trigger, yOffset]);

  return (
    <ReactCanvasConfetti
      onInit={onInitConfetti}
      style={{
        position: "fixed",
        pointerEvents: "none",
        width: "100%",
        height: "100%",
        top: 0,
        left: 0,
        zIndex: 999,
      }}
    />
  );
};

export default ConfettiExplosion;
