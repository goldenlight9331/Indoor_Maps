import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";

const laravelURL = import.meta.env.VITE_LARAVEL_URL;

export const userAuthApi = createApi({
  reducerPath: "userAuthApi",
  baseQuery: fetchBaseQuery({ baseUrl: `${laravelURL}/api` }),
  endpoints: (builder) => ({
    registerUser: builder.mutation({
      query: (user) => {
        return {
          url: "register",
          method: "POST",
          body: user,
          headers: {
            "Content-type": "application/json",
          },
        };
      },
    }),
    loginUser: builder.mutation({
      query: (user) => {
        return {
          url: "login",
          method: "POST",
          body: user,
          headers: {
            "Content-type": "application/json",
          },
        };
      },
    }),
    logoutUser: builder.mutation({
      query: ({ token }) => {
        return {
          url: "logout",
          method: "POST",
          body: {},
          headers: {
            authorization: `Bearer ${token}`,
          },
        };
      },
    }),
    // getLoggedUser: builder.query({
    //   query: (token) => {
    //     return {
    //       url: "logout",
    //       method: "POST",
    //       body: {},
    //       headers: {
    //         authorization: `Bearer${token}`,
    //       },
    //     };
    //   },
    // }),
    // getCompanyData: builder.query({
    //   query: (token) => {
    //     return {
    //       url: "company",
    //       method: "GET",
    //       body: {},
    //       headers: {
    //         authorization: `Bearer${token}`,
    //       },
    //     };
    //   },
    // }),
  }),
});

export const {
  useRegisterUserMutation,
  useLoginUserMutation,
  useLogoutUserMutation,
  // getCompanyDataMutation,
} = userAuthApi;
