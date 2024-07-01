import React from 'react';
import Header from '../../components/Header';
import { useTranslation } from 'react-i18next';
import '../../styles/home.css'; 

const Home = () => {
  const { t } = useTranslation();

  return (
    <div>
      <Header />
      <main>
        <h1>{t('home')}</h1>
        <p>
          NO MORE WASTE is dedicated to reducing food waste by collecting unsold goods and redistributing them to those in need. We provide various services to help individuals and organizations minimize waste, share resources, and save money.
        </p>
      </main>
      <footer>
        <p>&copy; 2023 NO MORE WASTE. All rights reserved.</p>
      </footer>
    </div>
  );
};

export default Home;
