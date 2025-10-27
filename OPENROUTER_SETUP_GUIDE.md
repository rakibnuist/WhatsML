# OpenRouter API Configuration for WhatsML

## 🚀 OpenRouter API Setup Guide

### What is OpenRouter?
OpenRouter is a unified API that provides access to multiple AI models from different providers, including free models. It's perfect for WhatsML's AI features.

### Your OpenRouter Configuration
- **API Key**: `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`
- **Base URL**: `https://openrouter.ai/api/v1`
- **Free Model**: `meta-llama/llama-3.1-8b-instruct:free`

## Available Free Models on OpenRouter

### 1. Meta Llama 3.1 8B Instruct (Recommended)
- **Model ID**: `meta-llama/llama-3.1-8b-instruct:free`
- **Provider**: Meta
- **Context**: 8K tokens
- **Best for**: General AI tasks, text generation, Q&A

### 2. Microsoft Phi-3 Mini
- **Model ID**: `microsoft/phi-3-mini-128k-instruct:free`
- **Provider**: Microsoft
- **Context**: 128K tokens
- **Best for**: Long conversations, document analysis

### 3. Google Gemma 2B
- **Model ID**: `google/gemma-2-2b-it:free`
- **Provider**: Google
- **Context**: 8K tokens
- **Best for**: Fast responses, simple tasks

### 4. Mistral 7B Instruct
- **Model ID**: `mistralai/mistral-7b-instruct:free`
- **Provider**: Mistral AI
- **Context**: 32K tokens
- **Best for**: Code generation, reasoning

## Configuration in WhatsML

### Environment Variables
```env
# OpenRouter Configuration
OPENAI_API_KEY=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
OPENROUTER_API_KEY=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
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
OPENROUTER_MODEL=meta-llama/llama-3.1-8b-instruct:free
```

### Laravel Configuration
Update `config/openai.php`:
```php
<?php

return [
    'api_key' => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct:free'),
    'timeout' => 30,
    'max_tokens' => 1000,
    'temperature' => 0.7,
];
```

## AI Features in WhatsML

### 1. Auto-Reply Generation
- Generate intelligent responses to WhatsApp messages
- Context-aware replies based on conversation history
- Multi-language support

### 2. Content Generation
- Create message templates
- Generate marketing content
- Write product descriptions

### 3. Customer Support
- Automated FAQ responses
- Intelligent ticket routing
- Sentiment analysis

### 4. Analytics & Insights
- Message sentiment analysis
- Customer behavior insights
- Performance recommendations

## Usage Examples

### Basic Text Generation
```php
use OpenAI\Laravel\Facades\OpenAI;

$response = OpenAI::chat()->create([
    'model' => 'meta-llama/llama-3.1-8b-instruct:free',
    'messages' => [
        ['role' => 'user', 'content' => 'Generate a WhatsApp message for customer support']
    ],
    'max_tokens' => 150,
    'temperature' => 0.7,
]);

$message = $response->choices[0]->message->content;
```

### Auto-Reply Generation
```php
public function generateAutoReply($customerMessage, $context = [])
{
    $prompt = "Generate a helpful auto-reply for this WhatsApp message: '{$customerMessage}'";
    
    if (!empty($context)) {
        $prompt .= "\nContext: " . implode(', ', $context);
    }
    
    $response = OpenAI::chat()->create([
        'model' => 'meta-llama/llama-3.1-8b-instruct:free',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 200,
        'temperature' => 0.5,
    ]);
    
    return $response->choices[0]->message->content;
}
```

### Content Template Generation
```php
public function generateTemplate($type, $businessInfo = [])
{
    $prompt = "Generate a {$type} WhatsApp message template for a business";
    
    if (!empty($businessInfo)) {
        $prompt .= " with the following details: " . json_encode($businessInfo);
    }
    
    $response = OpenAI::chat()->create([
        'model' => 'meta-llama/llama-3.1-8b-instruct:free',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 300,
        'temperature' => 0.6,
    ]);
    
    return $response->choices[0]->message->content;
}
```

## Free Tier Limits

### OpenRouter Free Models
- **Rate Limits**: Varies by model
- **Context Length**: 8K-128K tokens depending on model
- **No Cost**: Completely free to use
- **Reliability**: High uptime and availability

### Recommended Usage
- **Start with**: Meta Llama 3.1 8B Instruct
- **For long conversations**: Microsoft Phi-3 Mini
- **For fast responses**: Google Gemma 2B
- **For code tasks**: Mistral 7B Instruct

## Testing Your Setup

### Test API Connection
```php
Route::get('/test-openrouter', function () {
    try {
        $response = OpenAI::chat()->create([
            'model' => 'meta-llama/llama-3.1-8b-instruct:free',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, test message']
            ],
            'max_tokens' => 50,
        ]);
        
        return response()->json([
            'success' => true,
            'response' => $response->choices[0]->message->content
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
```

### Test Auto-Reply Generation
```php
Route::post('/test-auto-reply', function (Request $request) {
    $message = $request->input('message', 'Hello, I need help with my order');
    
    $autoReply = app('OpenAIService')->generateAutoReply($message);
    
    return response()->json([
        'original_message' => $message,
        'auto_reply' => $autoReply
    ]);
});
```

## Best Practices

### 1. Model Selection
- Use appropriate models for specific tasks
- Test different models for optimal results
- Consider context length requirements

### 2. Prompt Engineering
- Write clear, specific prompts
- Provide context when needed
- Use examples for better results

### 3. Error Handling
- Implement proper error handling
- Add fallback responses
- Monitor API usage and limits

### 4. Performance Optimization
- Cache frequently used responses
- Implement rate limiting
- Use appropriate temperature settings

## Troubleshooting

### Common Issues
1. **API Key Invalid**: Verify the key is correct
2. **Model Not Available**: Check model availability
3. **Rate Limits**: Implement proper throttling
4. **Context Too Long**: Reduce input length

### Debug Commands
```bash
# Test API connection
curl -X POST https://openrouter.ai/api/v1/chat/completions \
  -H "Authorization: Bearer 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" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "meta-llama/llama-3.1-8b-instruct:free",
    "messages": [{"role": "user", "content": "Hello"}],
    "max_tokens": 50
  }'
```

## Integration with WhatsML Features

### 1. QAReply Module
- Generate intelligent auto-replies
- Context-aware responses
- Multi-language support

### 2. Template Generation
- Create message templates
- Generate marketing content
- Write product descriptions

### 3. Customer Support
- Automated FAQ responses
- Intelligent ticket routing
- Sentiment analysis

### 4. Analytics
- Message sentiment analysis
- Customer behavior insights
- Performance recommendations

This OpenRouter configuration provides powerful AI capabilities for your WhatsML application at no cost, using free models that are perfect for commercial use.
