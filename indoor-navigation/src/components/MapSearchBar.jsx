import styles from "./CSS/MapSearch.module.css";

export default function MapSearchBar({ handleSearch, handleSuggestionClick, mapVisible, suggestions, searchTerm, toggleMapVisibility,})
{
  // Receive searchTerm prop
  return (
    <div className="container">
      <div className={`${styles["floor-search-bar"]}`}>

        <input
          type="text"
          placeholder="Search Navigation..."
          className={`${styles["search-input"]} form-control col-12 w-100`}
          autoComplete="off"
          onChange={handleSearch}
          value={searchTerm} // Use searchTerm prop here
        />
        {suggestions.length > 0 && (
          <ul className={styles["suggestions-dropdown"]}>
            {suggestions.map((company, index) => (
              <li
                key={index}
                className={styles["suggestion-item"]}
                onClick={() => handleSuggestionClick(company)}
              >
                {company.storename + " : " + company.category}
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
}
