import axiosClient from './axiosClient';

interface UserProfile {
  id: string;
  name: string;
  email: string;
}

const userApi = {
  getProfile: (): Promise<UserProfile> => axiosClient.get('/users/me'),
};

export default userApi;
