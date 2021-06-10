serviceName: ${SERVICE_NAME}
containers:
  nginx:
    command: []
    environment:
      test: test
    image: ${LATEST_NGINX_LIGHTSAIL_DOCKER_IMAGE}
    ports:
      "80": HTTP
  php:
    command: []
    environment:
      APP_NAME: "My App Name"
      DEBUG: "true"
      DATABASE_URL: "${DATABASE_URL}"
    image: ${LATEST_PHP_LIGHTSAIL_DOCKER_IMAGE}
publicEndpoint:
  containerName: nginx
  containerPort: 80
  healthCheck:
    healthyThreshold: 2
    intervalSeconds: 20
    path: /index.php
    successCodes: 200-499
    timeoutSeconds: 4
    unhealthyThreshold: 2
