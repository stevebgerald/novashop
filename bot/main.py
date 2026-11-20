import logging
import os
from telegram import Update
from telegram.ext import Application, CommandHandler, MessageHandler, filters
import responses

# token en el .env
ruta_env = os.path.join(os.path.dirname(os.path.abspath(__file__)), '.env')
if os.path.exists(ruta_env):
    for linea in open(ruta_env, encoding='utf-8'):
        if '=' in linea and not linea.startswith('#'):
            clave, valor = linea.strip().split('=', 1)
            os.environ.setdefault(clave, valor)

api_key = os.environ.get('TELEGRAM_TOKEN', '')

# set up logging

logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)
logging.info('Starting Bot....')


async def start_command(update: Update, context):
    await update.message.reply_text(responses.start())


async def help_command(update: Update, context):
    await update.message.reply_text(responses.ayuda())


async def custom_command(update: Update, context):
    partes = update.message.text.split(maxsplit=1)
    comando = partes[0][1:].split('@')[0].lower()
    argumentos = partes[1] if len(partes) > 1 else ''
    if comando == 'miid':
        await update.message.reply_text(f'Tu id de chat es: {update.message.chat.id}')
        return
    await update.message.reply_text(responses.get_command(comando, argumentos))


async def handle_message(update: Update, context):
    text = str(update.message.text).lower()
    logging.info(f'User({update.message.chat.id}) dice: {text} ')
    response = responses.get_responses(text)

    # Respuestas bot

    await update.message.reply_text(response)


async def error(update, context):
    logging.error(f'Update {update} causo el error {context.error}')


if __name__ == '__main__':
    if not api_key:
        raise SystemExit('Falta el token. Copiar .env.example como .env y completar TELEGRAM_TOKEN')

    application = Application.builder().token(api_key).build()

    # Comandos
    application.add_handler(CommandHandler('start', start_command))
    application.add_handler(CommandHandler('help', help_command))
    application.add_handler(MessageHandler(filters.COMMAND, custom_command))

    # Mensajes
    application.add_handler(MessageHandler(filters.TEXT, handle_message))

    # Errores
    application.add_error_handler(error)

    # Corre el bot
    application.run_polling(1.0)
