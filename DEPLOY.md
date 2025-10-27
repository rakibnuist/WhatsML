# WhatsML Deployment - Quick Start

## 🚀 Deploy Your WhatsML Project

### Prerequisites
- Railway CLI installed
- Mailtrap credentials configured
- OpenRouter API key ready

### Deploy Now
```bash
# 1. Install Railway CLI
npm install -g @railway/cli
railway login

# 2. Deploy your project
./deploy.sh
```

### Test After Deployment
```bash
# Test email
curl -X POST https://your-app.railway.app/api/test-email

# Test AI
curl -X POST https://your-app.railway.app/api/test-ai \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello"}'
```

### Cost: $0-5/month
- Railway: $5 credit
- Mailtrap: Free (100 emails/month)
- OpenRouter: Free models

## ✅ Ready to Launch!
