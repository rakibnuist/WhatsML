# 🔍 WhatsML Debugging Guide

## Overview
This guide provides comprehensive debugging tools and techniques for the WhatsML Laravel application.

## 🛠️ Debugging Tools Available

### 1. Debug Command
```bash
php artisan debug:info [options]
```

**Options:**
- `--check-db`: Check database connection
- `--check-cache`: Check cache connection  
- `--check-logs`: Check log files

**Example:**
```bash
php artisan debug:info --check-db --check-cache --check-logs
```

### 2. DebugHelper Class
A comprehensive debugging helper class with methods for:

- **General Logging**: `DebugHelper::log($message, $context, $level)`
- **API Calls**: `DebugHelper::logApiCall($method, $url, $data, $response)`
- **WhatsApp Operations**: `DebugHelper::logWhatsAppOperation($operation, $data, $result)`
- **OpenAI Operations**: `DebugHelper::logOpenAIOperation($operation, $data, $result)`
- **Performance Monitoring**: `DebugHelper::logPerformance($operation, $startTime, $additionalData)`
- **User Actions**: `DebugHelper::logUserAction($action, $data)`
- **Module Operations**: `DebugHelper::logModuleOperation($module, $operation, $data)`
- **Timers**: `DebugHelper::startTimer($name)` and `DebugHelper::endTimer($name)`

### 3. Enhanced Exception Handling
The `Handler.php` now includes:
- Comprehensive exception logging with context
- API-specific error handling
- JSON response formatting for API errors
- User and request context in logs

## 📝 Log Channels

### Available Log Channels:
- **stack**: Default channel (includes single + debug)
- **single**: Main application log (`storage/logs/laravel.log`)
- **debug**: Debug-specific log (`storage/logs/debug.log`)
- **api**: API-specific log (`storage/logs/api.log`)
- **daily**: Rotating daily logs

### Log Levels:
- `debug`: Detailed debugging information
- `info`: General information
- `warning`: Warning messages
- `error`: Error messages
- `critical`: Critical errors

## 🔧 Environment Configuration

### Critical Environment Variables:
- `APP_KEY`: Application encryption key
- `APP_DEBUG`: Enable/disable debug mode
- `DB_CONNECTION`: Database driver
- `MAIL_TO`: Admin email for contact form
- `WHATSAPP_WEB_API_KEY`: WhatsApp Web API key
- `QUEUE_MAIL`: Enable/disable mail queuing

### Debug-Specific Settings:
- `LOG_LEVEL`: Set minimum log level
- `CACHE_LIFETIME`: Cache duration in seconds

## 🚨 Common Issues & Solutions

### 1. Database Connection Issues
**Problem**: Database connection failures
**Solution**: 
```bash
php artisan debug:info --check-db
```
Check database credentials in `.env` file

### 2. Cache Issues
**Problem**: Cache not working
**Solution**:
```bash
php artisan debug:info --check-cache
```
Verify cache driver configuration

### 3. Email Not Sending
**Problem**: Contact form not sending emails
**Solution**: 
- Check `MAIL_TO` environment variable
- Verify mail configuration
- Check logs for email errors

### 4. API Key Issues
**Problem**: Hardcoded or missing API keys
**Solution**: 
- Set `WHATSAPP_WEB_API_KEY` in `.env`
- Check logs for API key warnings

## 📊 Performance Debugging

### Using Timers:
```php
$startTime = DebugHelper::startTimer('operation_name');
// ... your code ...
DebugHelper::endTimer('operation_name');
```

### Memory Usage Monitoring:
```php
DebugHelper::log('Memory Check', [
    'current_memory' => memory_get_usage(true),
    'peak_memory' => memory_get_peak_usage(true)
]);
```

## 🔍 Module-Specific Debugging

### WhatsApp Module:
```php
DebugHelper::logWhatsAppOperation('send_message', [
    'phone_number' => $phone,
    'message' => $message
], $response);
```

### OpenAI Module:
```php
DebugHelper::logOpenAIOperation('generate_response', [
    'prompt' => $prompt,
    'model' => $model
], $result);
```

## 📁 Log File Locations

- **Main Log**: `storage/logs/laravel.log`
- **Debug Log**: `storage/logs/debug.log`
- **API Log**: `storage/logs/api.log`
- **Daily Logs**: `storage/logs/laravel-YYYY-MM-DD.log`

## 🎯 Best Practices

1. **Use Appropriate Log Levels**: Don't log everything as `error`
2. **Include Context**: Always include relevant context in logs
3. **Monitor Performance**: Use timers for slow operations
4. **Check Logs Regularly**: Monitor log files for issues
5. **Use Debug Command**: Run `php artisan debug:info` regularly

## 🚀 Quick Debug Checklist

- [ ] Run `php artisan debug:info --check-db --check-cache --check-logs`
- [ ] Check environment variables are set
- [ ] Verify log files are being created
- [ ] Test email functionality
- [ ] Check API key configurations
- [ ] Monitor memory usage
- [ ] Review exception logs

## 📞 Support

For additional debugging help:
1. Check the logs in `storage/logs/`
2. Run the debug command with all options
3. Use DebugHelper methods in your code
4. Review the exception handler logs

---

**Last Updated**: October 27, 2025
**Version**: 1.0
