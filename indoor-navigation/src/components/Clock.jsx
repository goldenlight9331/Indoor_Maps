import React, { useState, useEffect } from 'react';
import './CSS/Clock.css';

import nightBackground from '../../public/night.jpeg'
import dayBackground from '../../public/day.jpeg'

const Clock = ({city, dayImage=null, nightImage=null, textColor=null}) => {
  const [dateTime, setDateTime] = useState(new Date());
  const [isDayTime, setIsDayTime] = useState(true);

  useEffect(() => {

    // Update date and time every second
    const timer = setInterval(() => {
      setDateTime(new Date());
    }, 60000);

    // Determine if it's day or night based on the hour
    const hour = dateTime.getHours();
    setIsDayTime(hour >= 6 && hour < 18);

    return () => clearInterval(timer);
  }, [dateTime]);

  const getBackgroundImage = () => {
    if (isDayTime) {
      if (dayImage) {
        return (`data:image/jpeg;base64,${dayImage}`);
      }
      return dayBackground;      
    }

    if (nightImage) {
      return (`data:image/jpeg;base64,${nightImage}`);
    }
    return nightBackground;
  };

  return (
    <div
      style={{
        backgroundImage: `url(${getBackgroundImage()})`,
        backgroundSize: "cover",
        display: "flex",
        height: '10%',
        justifyContent: "center",
        alignItems: "center",
      }}
    >
      <div className="date-time-content">
        <div className="date-time-line">
          <div className="date" style={{color: textColor}}>
            {dateTime.toLocaleDateString('en-US', {
              weekday: 'short',
              day: 'numeric',
              month: 'short',
            })}
          </div>
          <div className="time" style={{color: textColor}}>
            {dateTime.toLocaleTimeString('en-US', {
              hour: '2-digit',
              minute: '2-digit',
            })}
          </div>
        </div>
        <div className="location" style={{color: textColor}}>
            {city}
        </div>
      </div>
    </div>
);
};

export default Clock;
