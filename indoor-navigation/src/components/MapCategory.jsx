import './CSS/MapCategory.css';

export default function MapCategory({ categories, handleCategorySelection, selectedCategory }) {
    return (
        <div className="dropdown-menu">
            <span
                className="dropdown-item menu__item"
                onClick={() => handleCategorySelection('all')}
                style={{backgroundColor: selectedCategory === 'all' ? '#ADD8F7' : ''}}
            >
                <img
                    src={`/public/poiImages/all.png`}
                    className="category-icon"
                />
                <span className="category-name">All</span>
            </span>
            
            {categories.map((cat, index) => (
                <span
                    key={index} 
                    className="dropdown-item menu__item"
                    onClick={() => handleCategorySelection(cat.category)}
                    style={{backgroundColor: selectedCategory === cat.category ? '#ADD8F7' : ''}}
                >
                    <img
                        src={`/category/${cat.category.replace(/\s+/g, "-")}.png`}
                        alt={cat.category}
                        className="category-icon"
                    />
                    <span className="category-name">{cat.category}</span>
                </span>
            ))}
        </div>
    );
}
