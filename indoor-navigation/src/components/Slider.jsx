import "./CSS/slider.css";

export default function Slider({ slideIndex, buttonWidthPercent, slides, activeButton, handleButtonClick }) {
    return (
        <div className="floor-numbers">
            <div className="floor-text">Floors</div>

            <div className="floor-buttons">
                {slides.map((level, index) => (
                    <div key={index} className={`color slide ${index === slideIndex ? 'slide-enter' : 'slide-exit'}`} style={{ width: `${buttonWidthPercent}%` }}>
                        <button
                            className={`btn ${activeButton === level.levelid ? 'active' : 'inactive'}`}
                            onClick={() => handleButtonClick(level)}
                        >
                            {level.levelid}
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
}
