import './CSS/FilteredStores.css'

const FilteredStores = ({filteredCompanies, navigateToDashboard, themeColor}) => {
    return (
        <div className="filtered-companies" style={{backgroundColor: themeColor}}>
            {filteredCompanies.length > 0 ? (
                filteredCompanies.map((company, index) => (
                    <div key={index} className="company-item" onClick={() => navigateToDashboard(company.storename, company.levelid)}>
                        <img src={`data:image/png;base64,${company.image_data}`} alt={company.storename} className="company-image" />
                        <p className="company-name">{company.storename}</p>
                    </div>
                ))
                ) : (
                <b>No companies found for this category.</b>
            )}
        </div>
    );
};

export default FilteredStores;