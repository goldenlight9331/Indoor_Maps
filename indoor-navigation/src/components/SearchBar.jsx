import { useEffect, useRef, useState } from "react";
import loadDatabase from "../db/convertSQlite";

const INITIAL_SEARCH_TERM = "";
const PLACEHOLDER_TEXT = "Search users...";
const LOADING_TEXT = "Loading...";
const NO_USERS_FOUND_TEXT = "No users found.";

export default function SearchBar() {
  const [users, setUsers] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [filteredUsers, setFilteredUsers] = useState([]);
  const [searchTerm, setSearchTerm] = useState(INITIAL_SEARCH_TERM);
  const searchInputRef = useRef();

  const fetchUsers = async () => {
    setIsLoading(true);
    try {
      const response = await loadDatabase();
      setUsers(response.data);
    } catch (error) {
      console.error("Error fetching users:", error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  useEffect(() => {
    setFilteredUsers(
      users.filter((user) =>
        user.name.toLowerCase().includes(searchTerm.toLowerCase())
      )
    );
  }, [searchTerm, users]);

  const handleUserClick = (user) => {
    searchInputRef.current.value = user.STORENAME; // Assuming correct property
    setSearchTerm(INITIAL_SEARCH_TERM);
  };

  return (
    <div className="app">
      <div className="search">
        <div className="searchbox">
          <input
            type="text"
            placeholder={PLACEHOLDER_TEXT}
            id="search-input"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            ref={searchInputRef}
          />
        </div>
        {searchTerm.length > 0 && (
          <div className="dropdown">
            {isLoading ? (
              <p>{LOADING_TEXT}</p>
            ) : filteredUsers.length > 0 ? (
              <ul className="user-list">
                {filteredUsers.map((user) => (
                  <li key={user.id} className="user-card" onClick={() => handleUserClick(user)}>
                    <p>{user.STORENAME}</p>
                  </li>
                ))}
              </ul>
            ) : (
              <p>{NO_USERS_FOUND_TEXT}</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
