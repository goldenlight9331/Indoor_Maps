import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import Floor from './floor';
export default function FloorWrapper({ BackArrow }) {
  const [rotateReady, setRotateReady] = useState(false);
  const { STORENAME, LEVELID } = useParams();
  const loadRotatePlugin = () => {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet-rotate@0.2.8/dist/leaflet-rotate-src.js';
      script.onload = resolve;
      script.onerror = reject;
      document.body.appendChild(script);
    });
  };

  useEffect(() => {
    loadRotatePlugin().then(() => {
      setRotateReady(true);
    });
  }, []);

  if (!rotateReady) {
    return <div>Loading map rotation plugin...</div>; // or null/spinner
  }

  return <Floor BackArrow={BackArrow} STORENAME={STORENAME} LEVELID={LEVELID} />;
}
