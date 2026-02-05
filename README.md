# 🎓 Frequência Certa

![Status](https://img.shields.io/badge/Status-Concluído-brightgreen)
![Laravel](https://img.shields.io/badge/Laravel-12+-FF2D20?style=flat&logo=laravel&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat&logo=pwa&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

> **Sistema Inteligente de Gestão e Controle de Frequência Escolar.**

---

## 📖 Sobre o Projeto

O **Frequência Certa** é uma solução híbrida (Web e PWA) desenvolvida para empoderar estudantes do ensino médio e técnico no gerenciamento de sua assiduidade escolar.

Além de mitigar a evasão e auxiliar no cumprimento de requisitos para benefícios como o **Pé-de-Meia**, o sistema evoluiu para oferecer uma experiência de usuário moderna e engajadora. Operando como uma **Progressive Web App (PWA)**, o sistema oferece navegação fluida, gamificação para incentivar a constância e um assistente de IA para análise de riscos.

### 🚀 Novas Funcionalidades (v2.0)

- **⚡ Interface Moderna:** Design responsivo e interativo, otimizado para experiência mobile-first com animações sutis via GSAP.
- **🔮 Oráculo Acadêmico (IA):** Um assistente virtual integrado (Google Gemini) que analisa o histórico do aluno via chat e oferece conselhos personalizados sobre faltar ou não.
- **🏆 Gamificação & Conquistas:** Sistema de medalhas e "ofensivas" (streaks) para recompensar alunos que mantêm a frequência e o registro em dia.
- **🔔 Notificações Push:** Lembretes automáticos enviados diretamente ao dispositivo para registrar a chamada ou alertar sobre limites de faltas.

### 🌟 Funcionalidades Essenciais

- **📱 Mobile First & PWA:** Instalável no celular (Android/iOS) com suporte offline.
- **📅 Grade Dinâmica:** Montagem flexível de horários semanais.
- **📊 Inteligência de Dados:** Projeção automática de dias letivos e cálculo percentual em tempo real.
- **🚦 Alertas Semafóricos:** Indicadores visuais (Verde/Amarelo/Vermelho) de risco.
- **🗓️ Gestão de Eventos:** Cadastro de feriados e dias sem aula.

---

## 📸 Demonstração

<details>
  <summary align="center"><strong>Clique para ver o Modo Claro (Light Mode)</strong></summary>
  <br>

  <table align="center">
    <tr>
      <td align="center">
        <img src="public/img/screenshots/clean-browser-mockup-light.png" alt="Desktop Light" width="600">
      </td>
      <td align="center">
        <img src="public/img/screenshots/iphone-11-mockup-light.png" alt="Mobile Light" width="600">
      </td>
    </tr>
  </table>
</details>

<details>
  <summary align="center"><strong>Clique para ver o Modo Escuro (Dark Mode)</strong></summary>
  <br>

  <table align="center">
    <tr>
      <td align="center">
        <img src="public/img/screenshots/clean-browser-mockup-dark.png" alt="Desktop Dark" width="600">
      </td>
      <td align="center">
        <img src="public/img/screenshots/iphone-11-mockup-dark.png" alt="Mobile Dark" width="600">
      </td>
    </tr>
  </table>
</details>



---

## 🛠️ Stack Tecnológica

Arquitetura moderna foca em performance, interatividade e experiência do usuário (UX):

| Front-end & Interface | Back-end & Infraestrutura | Bibliotecas & Serviços |
| :--- | :--- | :--- |
| ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) **HTML5** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) **Laravel 12** | ![GSAP](https://img.shields.io/badge/GSAP-88CE02?style=flat-square&logo=greensock&logoColor=white) **GSAP (Animações)** |
| ![Tailwind](https://img.shields.io/badge/Tailwind-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white) **Tailwind CSS** | ![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white) **PHP 8.2** | ![Gemini](https://img.shields.io/badge/Google_Gemini-8E75B2?style=flat-square&logo=google-bard&logoColor=white) **Google Gemini AI** |
| ![AlpineJS](https://img.shields.io/badge/Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=black) **Alpine.js** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) **MySQL** | ![SweetAlert2](https://img.shields.io/badge/SweetAlert2-EF2D5E?style=flat-square&logo=alert&logoColor=white) **SweetAlert2** |
| ![PWA](https://img.shields.io/badge/PWA-Workbox-5A0FC8?style=flat-square&logo=pwa&logoColor=white) **Service Workers** | ![Filament](https://img.shields.io/badge/Filament-F28D15?style=flat-square&logo=laravel&logoColor=white) **Filament Admin** | ![Firebase](https://img.shields.io/badge/Firebase-FFCA28?style=flat-square&logo=firebase&logoColor=black) **Firebase Auth** |

---

## ✅ Pré-requisitos

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (ou MariaDB)

---

## 👥 Equipe de Desenvolvimento

| Integrante | Funções Principais | GitHub |
| :--- | :--- | :--- |
| **Nicolas Viana Alves** | Full-Stack, Documentação & Deploy | [@naicolas-dev](https://github.com/naicolas-dev) |
| **Bruno Felix Seixas** | Front-end, PWA & UI/UX | [@obrunofelix](https://github.com/obrunofelix) |
| **Igor Thiago Costa Rodrigues** | Back-end & QA | [@luxxzvh](https://github.com/luxxzvh) |

---

## 🚀 Como rodar o projeto localmente

1. Clone o repositório:
```bash
git clone <URL_DO_REPOSITORIO>
cd <NOME_DO_PROJETO>
```
2. Instale as dependências:
```bash
composer install
npm install
```
3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```
4. Configuração de APIs (Essencial): Adicione as chaves necessárias no seu arquivo .env:
```env
# API de Feriados (Para feriados estaduais)
INVERTEXTO_API_KEY=sua_chave_invertexto

# IA do Oráculo (Obrigatório para o chat)
GEMINI_API_KEY=sua_chave_google_gemini

# Notificações Push (WebPush)
VAPID_PUBLIC_KEY=sua_chave_publica_vapid
VAPID_PRIVATE_KEY=sua_chave_privada_vapid
```
5. Configure o banco de dados e migre:
```bash
php artisan migrate --seed
```
6. Inicie o servidor:

Terminal 1 (Backend):
```bash
php artisan serve
```
Terminal 2 (Frontend & Watcher):
```bash
npm run dev
```
Acesse o projeto em: http://localhost:8000

## ⚠️ Disclaimer

Este sistema foi desenvolvido **exclusivamente para fins acadêmicos**, como parte de um Trabalho de Conclusão de Curso (TCC).

As informações de frequência apresentadas pelo sistema representam **estimativas baseadas na grade horária definida pelo estudante, nos registros de presença realizados e nos dias não letivos informados**, não devendo ser interpretadas como registros oficiais ou documentos válidos para comprovação institucional.

O projeto **não substitui sistemas oficiais de controle escolar**, diários de classe ou registros administrativos das instituições de ensino.
## 📄 Licença
Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

---

<div align="center"> <sub>Projeto de caráter acadêmico e demonstrativo. <br>© Desenvolvido para o Curso Técnico em Desenvolvimento de Sistemas – 2025/2026.</sub> </div>
