# Configuração da Google Places API - Pemgir Malhas

## Visão Geral

A seção de depoimentos do site da Pemgir Malhas está configurada para buscar avaliações reais do Google Places API. Este documento explica como configurar e usar esta funcionalidade.

## Place ID da Pemgir Malhas

**Place ID:** `ChIJOeX0M2Y3390QjneamLa7sDk`

Este ID foi extraído do link do Google Maps fornecido:
```
https://www.google.com/search?q=pemgir#lrd=0x94df380633f4a539:0x30babd8b6b9a778e,1,,,,
```

## Configuração da API Key

### 1. Obtenha uma API Key do Google

1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto ou selecione um existente
3. Ative a **Places API**
4. Vá para "Credenciais" e crie uma nova API Key
5. Configure as restrições da API Key:
   - **Restrições de aplicativo:** HTTP referrers (sites)
   - **Adicione seu domínio:** `https://seudominio.com/*`
   - **Restrições de API:** Places API

### 2. Configure a variável de ambiente

Adicione a API Key ao seu arquivo `.env`:

```bash
GOOGLE_PLACES_API_KEY=SUA_API_KEY_AQUI
```

### 3. Estrutura da Resposta

A API retorna dados no seguinte formato:

```javascript
{
  "result": {
    "reviews": [
      {
        "author_name": "Nome do Usuário",
        "author_url": "https://www.google.com/maps/contrib/...",
        "language": "pt",
        "profile_photo_url": "https://lh3.googleusercontent.com/...",
        "rating": 5,
        "relative_time_description": "há 2 meses",
        "text": "Texto da avaliação...",
        "time": 1642694400
      }
    ]
  }
}
```

## Funcionalidades Implementadas

### 1. Dados de Fallback
Se a API Key não estiver configurada, o sistema usa dados de exemplo realistas da Pemgir Malhas.

### 2. Loading State
Spinner é exibido enquanto as avaliações são carregadas.

### 3. Tratamento de Erro
Fallback gracioso para dados de exemplo em caso de erro na API.

### 4. Formatação de Data
Datas são formatadas no padrão brasileiro (dd de mês de aaaa).

### 5. Carousel Automático
As avaliações passam automaticamente a cada 3 segundos.

## Custos da API

A Google Places API tem os seguintes custos (valores de referência):
- **Place Details:** $17.00 por 1.000 solicitações
- **Cota gratuita:** $200 por mês

Para um site com tráfego normal, os custos serão mínimos.

## Limitações e Considerações

### 1. CORS
A API do Google Places não permite chamadas diretas do frontend por questões de CORS. Para produção, considere:

- **Opção 1:** Criar um proxy no backend
- **Opção 2:** Usar a Google Places API do lado do servidor
- **Opção 3:** Usar dados pré-buscados e atualizados periodicamente

### 2. Rate Limiting
A API tem limites de taxa. Para sites com muito tráfego, considere implementar cache.

### 3. Segurança
Nunca exponha a API Key no frontend. Use sempre variáveis de ambiente.

## Implementação em Produção

Para produção, recomendamos criar um endpoint no backend:

```javascript
// Backend (Node.js/Express)
app.get('/api/reviews', async (req, res) => {
  try {
    const response = await fetch(
      `https://maps.googleapis.com/maps/api/place/details/json?place_id=ChIJOeX0M2Y3390QjneamLa7sDk&fields=reviews&key=${process.env.GOOGLE_PLACES_API_KEY}`
    );
    const data = await response.json();
    res.json(data.result.reviews);
  } catch (error) {
    res.status(500).json({ error: 'Erro ao buscar avaliações' });
  }
});
```

E no frontend:

```javascript
const fetchGoogleReviews = async () => {
  try {
    const response = await fetch('/api/reviews');
    const reviews = await response.json();
    googleReviews.value = reviews.map((review, index) => ({
      // formatação dos dados...
    }));
  } catch (error) {
    console.error('Erro ao buscar avaliações:', error);
  }
};
```

## Teste

Para testar a funcionalidade:

1. Configure a API Key
2. Abra o console do navegador
3. Verifique se há logs de sucesso ou erro
4. Observe se as avaliações são carregadas na seção de depoimentos

## Suporte

Para dúvidas sobre a implementação:
- Documentação oficial: [Places API Documentation](https://developers.google.com/maps/documentation/places/web-service/overview)
- Console do Google Cloud: [Cloud Console](https://console.cloud.google.com/)