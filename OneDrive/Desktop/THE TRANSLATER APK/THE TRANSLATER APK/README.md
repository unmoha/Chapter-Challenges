# Translator Desktop (JavaFX)

A cross-platform desktop translation app (Google-Translate-like) built with Java 17 + JavaFX, designed with Clean Architecture and pluggable translation engines.

## Requirements

- Java 17+
- Maven 3.9+

## Run (dev)

From the project root:

- `mvnw.cmd -pl ui-fx -am org.openjfx:javafx-maven-plugin:0.0.8:run`

Alternative (run from the UI module folder):

- `cd ui-fx`
- `..\mvnw.cmd -f ..\pom.xml -pl ui-fx -am org.openjfx:javafx-maven-plugin:0.0.8:run`

If you want to run using only `ui-fx/pom.xml`, first install the other modules into your local Maven repository:

- `mvnw.cmd -DskipTests install`
- `cd ui-fx`
- `..\mvnw.cmd org.openjfx:javafx-maven-plugin:0.0.8:run`

## Run as Server

To run the application in server mode (RMI server for remote translation):

- `java -jar ui-fx/target/translator-desktop-0.1.0-jar-with-dependencies.jar --server`

This starts an RMI server on port 1099 that other clients can connect to.

## LibreTranslate (no API key)

By default the app points to a local LibreTranslate server:

- `http://localhost:5000`

You can change this in code initially (`LibreTranslateConfig`). A future step is to persist it in a settings file.

## Packaging (jpackage)

See the notes in the assistant guidance for `jlink` / `jpackage`.
