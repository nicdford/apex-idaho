# APEX Idaho web: Next.js standalone server.
#
#   docker build -t apex-idaho-web \
#     --build-arg WORDPRESS_URL=https://apex-idaho.com \
#     --build-arg NEXT_PUBLIC_SITE_URL=https://apex-idaho.com .
#   docker run -p 8080:8080 -e REVALIDATE_SECRET=... apex-idaho-web
#
# WORDPRESS_URL, WORDPRESS_PUBLIC_URL and NEXT_PUBLIC_SITE_URL are needed at
# build time (pages are prerendered, and redirects and image hosts are fixed
# into the build) and are carried into the image as runtime defaults.
# REVALIDATE_SECRET is runtime only: pass it with -e or `fly secrets set`.

FROM node:22-alpine AS deps
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

FROM node:22-alpine AS build
WORKDIR /app
ARG WORDPRESS_URL=https://apex-idaho.com
ARG WORDPRESS_PUBLIC_URL=
ARG NEXT_PUBLIC_SITE_URL=https://apex-idaho.com
ENV WORDPRESS_URL=$WORDPRESS_URL \
    WORDPRESS_PUBLIC_URL=$WORDPRESS_PUBLIC_URL \
    NEXT_PUBLIC_SITE_URL=$NEXT_PUBLIC_SITE_URL \
    NEXT_TELEMETRY_DISABLED=1
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build

FROM node:22-alpine AS runtime
WORKDIR /app
ARG WORDPRESS_URL=https://apex-idaho.com
ARG WORDPRESS_PUBLIC_URL=
ARG NEXT_PUBLIC_SITE_URL=https://apex-idaho.com
ENV NODE_ENV=production \
    NEXT_TELEMETRY_DISABLED=1 \
    PORT=8080 \
    HOSTNAME=0.0.0.0 \
    WORDPRESS_URL=$WORDPRESS_URL \
    WORDPRESS_PUBLIC_URL=$WORDPRESS_PUBLIC_URL \
    NEXT_PUBLIC_SITE_URL=$NEXT_PUBLIC_SITE_URL
COPY --from=build --chown=node:node /app/.next/standalone ./
COPY --from=build --chown=node:node /app/.next/static ./.next/static
COPY --from=build --chown=node:node /app/public ./public
USER node
EXPOSE 8080
CMD ["node", "server.js"]
