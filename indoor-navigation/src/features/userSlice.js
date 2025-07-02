import { createSlice } from "@reduxjs/toolkit";

export const userSlice = createSlice({
  name: "user_info",
  initialState: {
    email: "",
    name: "",
  },
  reducers: {
    setUserInfo: (state, action) => {
      state.email = action.payload.email;
      state.name = action.payload.name;
    },
    unsetUserInfo: (state, action) => {
      state.email = action.payload.email;
      state.name = action.payload.name;
    },
  },
});

export const { setUserInfo, unsetUserInfo } = userSlice.actions;

export default userSlice.reducer;
