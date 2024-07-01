import React from 'react';
import Header from '../../components/Header';
import Footer from '../../components/Footer';
import { useTranslation } from 'react-i18next';
import styled from 'styled-components';

const Main = styled.main`
  padding: 20px;
  background-color: #f4f4f4;
`;

const Title = styled.h1`
  font-size: 2.5em;
  color: #f97e1c;
  margin-bottom: 20px;
  text-align: center;
`;

const Section = styled.section`
  margin-bottom: 20px;
`;

const Paragraph = styled.p`
  font-size: 1.2em;
  line-height: 1.5;
  margin-bottom: 15px;
`;

const Home = () => {
  const { t } = useTranslation();

  return (
    <div>
      <Header />
      <Main>
        <Title>{t('welcome')}</Title>
        <Section>
          <Paragraph>
            {t('description')}
          </Paragraph>
          <Paragraph>
            {t('founded')}
          </Paragraph>
        </Section>
        <Section>
          <h2>{t('services_offered')}</h2>
          <ul>
            <li>{t('anti_waste_tips')}</li>
            <li>{t('cooking_classes')}</li>
            <li>{t('vehicle_sharing')}</li>
            <li>{t('service_exchange')}</li>
            <li>{t('repair_services')}</li>
            <li>{t('guarding')}</li>
          </ul>
        </Section>
      </Main>
      <Footer />
    </div>
  );
};

export default Home;
