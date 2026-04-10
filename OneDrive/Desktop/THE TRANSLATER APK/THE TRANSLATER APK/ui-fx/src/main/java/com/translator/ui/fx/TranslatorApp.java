package com.translator.ui.fx;

import com.translator.application.EngineRegistry;
import com.translator.application.TranslationService;
import com.translator.domain.Language;
import com.translator.domain.TranslationEngine;
import com.translator.domain.TranslationRequest;
import com.translator.engine.libretranslate.LibreTranslateConfig;
import com.translator.engine.libretranslate.LibreTranslateEngine;
import com.translator.engine.rmi.RmiTranslationEngine;
import net.sourceforge.tess4j.Tesseract;
import javafx.animation.FadeTransition;
import javafx.animation.Interpolator;
import javafx.animation.RotateTransition;
import javafx.application.Application;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.concurrent.Task;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.input.Clipboard;
import javafx.scene.input.ClipboardContent;
import javafx.scene.input.KeyCode;
import javafx.scene.input.KeyCodeCombination;
import javafx.scene.input.KeyCombination;
import javafx.scene.layout.*;
import javafx.scene.shape.SVGPath;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import javafx.util.Duration;
import javafx.util.StringConverter;
import com.translator.ui.theme.ThemeManager;
import org.vosk.Model;
import org.vosk.Recognizer;

import javax.imageio.ImageIO;
import javax.sound.sampled.AudioFileFormat;
import javax.sound.sampled.AudioFormat;
import javax.sound.sampled.AudioInputStream;
import javax.sound.sampled.AudioSystem;
import javax.sound.sampled.DataLine;
import javax.sound.sampled.LineUnavailableException;
import javax.sound.sampled.TargetDataLine;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.URI;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.ArrayList;
import java.util.List;
import java.util.prefs.Preferences;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;

public final class TranslatorApp extends Application {

    @Override
    public void start(Stage stage) {
        // Load theme preference first
        boolean isDarkTheme = loadDarkThemePreference();
        
        // Apply the theme to the application
        BorderPane root = new BorderPane();
        Scene scene = new Scene(root, 1000, 700);
        ThemeManager.applyTheme(scene, isDarkTheme ? ThemeManager.Theme.DARK : ThemeManager.Theme.LIGHT);
        
        // Set up the stage
        stage.setTitle("Translator");
        stage.setScene(scene);
        stage.setMinWidth(800);
        stage.setMinHeight(600);
        
        // Show the stage
        stage.show();
        
        // Initialize the rest of the application
        EngineRegistry registry = new EngineRegistry();
        String rmiHost = System.getProperty("rmi.host", System.getenv("RMI_HOST"));
        if (rmiHost != null && !rmiHost.isBlank()) {
            registry.register(new RmiTranslationEngine(rmiHost.trim()));
        }
        registry.register(new LibreTranslateEngine(new LibreTranslateConfig(URI.create("http://127.0.0.1:5000"))));

        TranslationService translationService = new TranslationService(registry.all().iterator().next());

        TranslatorHistoryStore historyStore = new TranslatorHistoryStore(
                Path.of(System.getProperty("user.home"), ".translator-desktop", "history.tsv")
        );

        ComboBox<TranslationEngine> engineCombo = new ComboBox<>(FXCollections.observableArrayList(registry.all()));
        engineCombo.getSelectionModel().selectFirst();
        engineCombo.setConverter(new StringConverter<>() {
            @Override
            public String toString(TranslationEngine engine) {
                return engine == null ? "" : engine.displayName();
            }

            @Override
            public TranslationEngine fromString(String string) {
                return null;
            }
        });

        ComboBox<Language> sourceLang = new ComboBox<>();
        ComboBox<Language> targetLang = new ComboBox<>();

        TextArea input = new TextArea();
        input.setWrapText(true);
        TextArea output = new TextArea();
        output.setWrapText(true);
        output.setEditable(false);

        Label status = new Label("Ready");

        Runnable refreshLanguages = () -> {
            TranslationEngine engine = engineCombo.getSelectionModel().getSelectedItem();
            if (engine == null) return;

            sourceLang.setItems(FXCollections.observableArrayList(engine.supportedLanguages()));
            targetLang.setItems(FXCollections.observableArrayList(engine.supportedLanguages()));

            sourceLang.getSelectionModel().selectFirst();
            for (Language lang : engine.supportedLanguages()) {
                if ("en".equals(lang.code())) {
                    targetLang.getSelectionModel().select(lang);
                    break;
                }
            }
        };

        engineCombo.setOnAction(e -> {
            TranslationEngine engine = engineCombo.getSelectionModel().getSelectedItem();
            if (engine != null) {
                translationService.setEngine(engine);
                refreshLanguages.run();
                status.setText("Engine: " + engine.displayName());
            }
        });

        refreshLanguages.run();

        Button swap = new Button();
        swap.getStyleClass().addAll("icon-button");
        swap.setGraphic(icon("M7 7h11l-2-2 1.4-1.4L22.8 9l-5.4 5.4L16 13l2-2H7V7zm10 10H6l2 2-1.4 1.4L1.2 15l5.4-5.4L8 11l-2 2h11v4z"));
        swap.setTooltip(new Tooltip("Swap languages"));

        RotateTransition swapAnim = new RotateTransition(Duration.millis(160), swap);
        swapAnim.setInterpolator(Interpolator.EASE_BOTH);
        swapAnim.setByAngle(180);

        Button translate = new Button("Translate");
        translate.setDefaultButton(true);
        translate.getStyleClass().addAll("translate-button");

        Button copy = new Button();
        copy.getStyleClass().addAll("icon-button");
        copy.setGraphic(icon("M8 7a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2V7zm2 0v12h9V7h-9zM3 5a2 2 0 0 1 2-2h9v2H5v12H3V5z"));
        copy.setTooltip(new Tooltip("Copy translation"));
        copy.setOnAction(ev -> copyToClipboard(output.getText()));

        Button record = new Button();
        record.getStyleClass().addAll("icon-button");
        record.setGraphic(icon("M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-6-4zM7 5h8v4H7V5zm12 14H5V5h0v6h12a2 2 0 0 1 2 2v6z"));
        record.setTooltip(new Tooltip("Save to history"));

        Button readHistory = new Button();
        readHistory.getStyleClass().addAll("icon-button");
        readHistory.setGraphic(icon("M12 22a10 10 0 1 1 10-10 10 10 0 0 1-10 10zm0-18a8 8 0 1 0 8 8 8 8 0 0 0-8-8zm1 8.4 3.3 2a1 1 0 0 1-1 1.7l-3.8-2.3A1 1 0 0 1 11 13V7a1 1 0 0 1 2 0v5.4z"));
        readHistory.setTooltip(new Tooltip("History"));

        Button readOutput = new Button();
        readOutput.getStyleClass().addAll("icon-button");
        readOutput.setGraphic(icon("M12 3a1 1 0 0 1 1 1v16a1 1 0 0 1-2 0V4a1 1 0 0 1 1-1zm4 2.2a1 1 0 0 1 1.4-.2A9 9 0 0 1 17.4 19a1 1 0 1 1-1.2-1.6 7 7 0 0 0 0-11.2 1 1 0 0 1-.2-1.4zm-8 0a1 1 0 0 1-.2 1.4 7 7 0 0 0 0 11.2A1 1 0 1 1 6.6 19 9 9 0 0 1 6.6 5a1 1 0 0 1 1.4.2z"));
        readOutput.setTooltip(new Tooltip("Read translation"));
        readOutput.setOnAction(ev -> {
            String out = output.getText() == null ? "" : output.getText().trim();
            if (out.isEmpty()) {
                status.setText("Nothing to read");
                return;
            }

            Language tgt = targetLang.getSelectionModel().getSelectedItem();
            String langCode = tgt == null ? null : tgt.code();

            status.setText("Reading...");
            readOutput.setDisable(true);
            Thread t = new Thread(() -> {
                try {
                    speakText(out, langCode);
                    Platform.runLater(() -> {
                        status.setText("Done");
                        readOutput.setDisable(false);
                    });
                } catch (Exception ex) {
                    Platform.runLater(() -> {
                        status.setText("Error: " + formatError(ex));
                        readOutput.setDisable(false);
                    });
                }
            }, "tts-read");
            t.setDaemon(true);
            t.start();
        });

        translate.setOnAction(e -> {
            TranslationEngine engine = translationService.engine();
            Language src = sourceLang.getSelectionModel().getSelectedItem();
            Language tgt = targetLang.getSelectionModel().getSelectedItem();

            if (engine == null || src == null || tgt == null) return;

            String text = input.getText() == null ? "" : input.getText().trim();
            if (text.isEmpty()) {
                output.setText("");
                status.setText("Nothing to translate");
                return;
            }

            status.setText("Translating via " + engine.displayName() + "...");
            translate.setDisable(true);

            Task<String> task = new Task<>() {
                @Override
                protected String call() throws Exception {
                    TranslationRequest request = new TranslationRequest(text, src.code(), tgt.code());
                    return translationService.translate(request).translatedText();
                }
            };

            task.setOnSucceeded(ev -> {
                output.setText(task.getValue());
                status.setText("Done");
                translate.setDisable(false);
            });

            task.setOnFailed(ev -> {
                Throwable ex = task.getException();
                output.setText("");
                status.setText("Error: " + formatError(ex));
                translate.setDisable(false);
            });

            Thread t = new Thread(task, "translate-task");
            t.setDaemon(true);
            t.start();
        });

        Button clearOutput = new Button();
        clearOutput.getStyleClass().addAll("icon-button");
        clearOutput.setGraphic(icon("M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"));
        clearOutput.setTooltip(new Tooltip("Clear output"));
        clearOutput.setOnAction(ev -> output.setText(""));

        Button pasteInput = new Button();
        pasteInput.getStyleClass().addAll("icon-button");
        pasteInput.setGraphic(icon("M8 4h8v2H8V4zm-2 0H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1h-2v1H5V6h1V4zm14 5h-7a1 1 0 0 0-1 1v7h2v-6h6V9zm2 2v10a1 1 0 0 1-1 1h-10v-2h9V12h2z"));
        pasteInput.setTooltip(new Tooltip("Paste"));
        pasteInput.setOnAction(ev -> {
            String s = Clipboard.getSystemClipboard().getString();
            if (s != null) input.insertText(input.getCaretPosition(), s);
        });

        Button clearInput = new Button();
        clearInput.getStyleClass().addAll("icon-button");
        clearInput.setGraphic(icon("M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"));
        clearInput.setTooltip(new Tooltip("Clear input"));
        clearInput.setOnAction(ev -> input.setText(""));

        Button mic = new Button();
        mic.getStyleClass().addAll("icon-button");
        mic.setGraphic(icon("M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.9V21h2v-3.1A7 7 0 0 0 19 11h-2z"));
        mic.setTooltip(new Tooltip("Voice input (offline Vosk)"));
        mic.setOnAction(ev -> {
            List<Path> modelCandidates = resolveVoskModelCandidates();
            if (modelCandidates.isEmpty()) {
                String userProfile = System.getenv("USERPROFILE");
                String home = System.getProperty("user.home");
                status.setText(
                        "Voice setup needed: no Vosk model found. Put model under "
                                + (userProfile == null ? "%USERPROFILE%" : userProfile)
                                + "\\.translator-desktop\\vosk-model (or set VOSK_MODEL_PATH). Checked: "
                                + (userProfile == null ? "%USERPROFILE%" : userProfile) + " and " + home
                );
                return;
            }

            status.setText("Listening (8s)...");
            mic.setDisable(true);
            translate.setDisable(true);

            Task<String> sttTask = new Task<>() {
                @Override
                protected String call() throws Exception {
                    return runVoskOnMic(modelCandidates, 8);
                }
            };

            sttTask.setOnSucceeded(done -> {
                String text = sttTask.getValue() == null ? "" : sttTask.getValue().trim();
                input.setText(text);
                mic.setDisable(false);
                translate.setDisable(false);
                status.setText(text.isEmpty() ? "Voice: no speech detected" : "Voice ready");
                if (!text.isEmpty()) translate.fire();
            });

            sttTask.setOnFailed(fail -> {
                Throwable ex = sttTask.getException();
                mic.setDisable(false);
                translate.setDisable(false);
                status.setText("Voice error: " + formatError(ex));
            });

            Thread t = new Thread(sttTask, "vosk-stt");
            t.setDaemon(true);
            t.start();
        });

        Button imageInput = new Button();
        imageInput.getStyleClass().addAll("icon-button");
        imageInput.setGraphic(icon("M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 13.5 11 16l3.5-4.5L19 17H5l3.5-3.5zM8 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"));
        imageInput.setTooltip(new Tooltip("Image to text (OCR)"));
        imageInput.setOnAction(ev -> {
            FileChooser chooser = new FileChooser();
            chooser.setTitle("Choose image for OCR");
            chooser.getExtensionFilters().addAll(
                    new FileChooser.ExtensionFilter("Images", "*.png", "*.jpg", "*.jpeg", "*.bmp", "*.gif"),
                    new FileChooser.ExtensionFilter("All Files", "*.*")
            );
            File file = chooser.showOpenDialog(stage);
            if (file == null) return;

            String tessdata = resolveTessdataPath();
            if (tessdata == null) {
                status.setText("OCR needs tessdata. Set TESSDATA_PREFIX or put tessdata in %USERPROFILE%\\.translator-desktop\\tessdata");
                return;
            }

            status.setText("Reading image...");
            translate.setDisable(true);
            imageInput.setDisable(true);

            Language src = sourceLang.getSelectionModel().getSelectedItem();
            String ocrLang = mapToTesseractLang(src == null ? null : src.code());

            Task<String> ocrTask = new Task<>() {
                @Override
                protected String call() throws Exception {
                    BufferedImage img = ImageIO.read(file);
                    if (img == null) throw new IOException("Unsupported image");
                    Tesseract t = new Tesseract();
                    t.setDatapath(tessdata);
                    t.setLanguage(ocrLang);
                    return t.doOCR(img);
                }
            };

            ocrTask.setOnSucceeded(done -> {
                String text = ocrTask.getValue() == null ? "" : ocrTask.getValue().trim();
                input.setText(text);
                status.setText(text.isEmpty() ? "OCR: no text found" : "OCR done");
                imageInput.setDisable(false);
                translate.setDisable(false);
                if (!text.isEmpty()) translate.fire();
            });

            ocrTask.setOnFailed(fail -> {
                Throwable ex = ocrTask.getException();
                status.setText("OCR error: " + formatError(ex));
                imageInput.setDisable(false);
                translate.setDisable(false);
            });

            Thread t = new Thread(ocrTask, "ocr-task");
            t.setDaemon(true);
            t.start();
        });

        record.setOnAction(ev -> {
            TranslationEngine engine = translationService.engine();
            Language src = sourceLang.getSelectionModel().getSelectedItem();
            Language tgt = targetLang.getSelectionModel().getSelectedItem();
            String in = input.getText() == null ? "" : input.getText().trim();
            String out = output.getText() == null ? "" : output.getText().trim();
            if (engine == null || src == null || tgt == null || in.isEmpty() || out.isEmpty()) {
                status.setText("Nothing to record");
                return;
            }
            try {
                historyStore.append(engine.id(), src.code(), tgt.code(), in, out);
                status.setText("Recorded");
            } catch (IOException ex) {
                status.setText("Error: " + formatError(ex));
            }
        });

        readHistory.setOnAction(ev -> {
            try {
                String content = historyStore.readAll();
                TextArea area = new TextArea(content);
                area.setWrapText(false);
                area.setEditable(false);
                Dialog<Void> dlg = new Dialog<>();
                dlg.setTitle("History");
                dlg.getDialogPane().setContent(area);
                dlg.getDialogPane().getButtonTypes().add(ButtonType.CLOSE);
                dlg.getDialogPane().setPrefSize(800, 450);
                dlg.initOwner(stage);
                dlg.showAndWait();
            } catch (IOException ex) {
                status.setText("Error: " + formatError(ex));
            }
        });

        Button quit = new Button("Quit");
        quit.getStyleClass().addAll("secondary-button");
        quit.setOnAction(e -> Platform.exit());

        Label engineLbl = new Label("Engine");
        engineLbl.getStyleClass().addAll("label");
        Label fromLbl = new Label("From");
        fromLbl.getStyleClass().addAll("label");
        Label toLbl = new Label("To");
        toLbl.getStyleClass().addAll("label");

        engineCombo.getStyleClass().addAll("combo-box");
        sourceLang.getStyleClass().addAll("combo-box");
        targetLang.getStyleClass().addAll("combo-box");

        root.getStyleClass().addAll("app-root");
        root.setPadding(new Insets(16));

        boolean initialDark = loadDarkThemePreference();
        if (initialDark) {
            status.setText("Theme: Dark");
        }

        Button themeToggle = new Button();
        themeToggle.getStyleClass().addAll("icon-button");
        themeToggle.setGraphic(icon("M12 3v1m0 16v-1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707.707"));
        themeToggle.setTooltip(new Tooltip("Toggle theme"));

        HBox toolbar = new HBox(10,
                engineLbl, engineCombo,
                new Region(),
                fromLbl, sourceLang,
                swap,
                toLbl, targetLang,
                readHistory,
                new Region(),
                themeToggle
        );
        HBox.setHgrow(toolbar.getChildren().get(2), Priority.ALWAYS);
        HBox.setHgrow(toolbar.getChildren().get(9), Priority.ALWAYS);
        toolbar.setAlignment(Pos.CENTER_LEFT);

        StackPane toolbarCard = new StackPane(toolbar);
        toolbarCard.getStyleClass().addAll("card", "toolbar-card");

        Label inputTitle = new Label("Input");
        inputTitle.getStyleClass().addAll("title");
        Region inputHeaderSpacer = new Region();
        HBox.setHgrow(inputHeaderSpacer, Priority.ALWAYS);
        HBox inputHeader = new HBox(8, inputTitle, inputHeaderSpacer, imageInput, mic, pasteInput, clearInput);
        inputHeader.getStyleClass().addAll("panel-header");

        Label outputTitle = new Label("Output");
        outputTitle.getStyleClass().addAll("title");
        Region outputHeaderSpacer = new Region();
        HBox.setHgrow(outputHeaderSpacer, Priority.ALWAYS);
        HBox outputHeader = new HBox(8, outputTitle, outputHeaderSpacer, copy, clearOutput, readOutput);
        outputHeader.getStyleClass().addAll("panel-header");

        input.getStyleClass().addAll("text-area");
        output.getStyleClass().addAll("text-area");

        VBox inputCard = new VBox(10, inputHeader, input);
        inputCard.getStyleClass().addAll("card", "panel-card");

        VBox outputCard = new VBox(10, outputHeader, output);
        outputCard.getStyleClass().addAll("card", "panel-card");

        SplitPane split = new SplitPane(inputCard, outputCard);
        split.setDividerPositions(0.5);

        status.getStyleClass().addAll("label");

        HBox actionButtons = new HBox(10, quit, translate);
        actionButtons.setAlignment(Pos.CENTER_RIGHT);

        HBox actionBar = new HBox(12, status, new Region(), actionButtons);
        HBox.setHgrow(actionBar.getChildren().get(1), Priority.ALWAYS);
        actionBar.getStyleClass().addAll("action-bar");

        VBox bottom = new VBox(10, actionBar);

        root.setTop(toolbarCard);
        root.setCenter(split);
        root.setBottom(bottom);

        // Apply the theme and styles
        // Scene is already created above, just update its size if needed
        // Theme is already applied above via ThemeManager.applyTheme()
        // No need to load additional CSS

        // Set up keyboard shortcuts
        scene.getAccelerators().put(new KeyCodeCombination(KeyCode.ENTER, KeyCombination.CONTROL_DOWN), translate::fire);
        scene.getAccelerators().put(new KeyCodeCombination(KeyCode.L, KeyCombination.CONTROL_DOWN), clearInput::fire);
        scene.getAccelerators().put(new KeyCodeCombination(KeyCode.K, KeyCombination.CONTROL_DOWN), clearOutput::fire);
        scene.getAccelerators().put(new KeyCodeCombination(KeyCode.C, KeyCombination.CONTROL_DOWN, KeyCombination.SHIFT_DOWN), copy::fire);

        // Set up the stage
        stage.setTitle("Translator Desktop");
        stage.setScene(scene);
        stage.show();

        // Now that scene is final, set up theme toggle
        themeToggle.setOnAction(e -> {
            boolean dark = !loadDarkThemePreference();
            ThemeManager.Theme theme = dark ? ThemeManager.Theme.DARK : ThemeManager.Theme.LIGHT;
            ThemeManager.applyTheme(scene, theme);
            saveDarkThemePreference(dark);

            root.applyCss();
            root.layout();
            status.setText(dark ? "Theme: Dark" : "Theme: Light");

            FadeTransition ft = new FadeTransition(Duration.millis(200), root);
            ft.setInterpolator(Interpolator.EASE_BOTH);
            ft.setFromValue(0.5);
            ft.setToValue(1);
            ft.play();
        });

        swap.setOnAction(e -> {
            Language src = sourceLang.getSelectionModel().getSelectedItem();
            Language tgt = targetLang.getSelectionModel().getSelectedItem();
            if (src == null || tgt == null) return;
            swapAnim.playFromStart();
            sourceLang.getSelectionModel().select(tgt);
            targetLang.getSelectionModel().select(src);
        });

    }

    private boolean loadDarkThemePreference() {
        // Load theme preference from user preferences or system settings
        // Default to light theme if no preference is found
        Preferences prefs = Preferences.userRoot();
        return prefs.getBoolean("dark-theme", false);
    }
    
    private void saveDarkThemePreference(boolean isDark) {
        // Save theme preference to user preferences or system settings
        // This is a placeholder implementation
        Preferences prefs = Preferences.userRoot();
        prefs.putBoolean("dark-theme", isDark);
    }
    
    private String formatError(Throwable ex) {
        if (ex == null) return "Unknown";
        String msg = ex.getMessage();
        if (msg != null && !msg.isBlank()) {
            return ex.getClass().getSimpleName() + ": " + msg;
        }
        return ex.toString();
    }

    private static SVGPath icon(String svgPathData) {
        SVGPath p = new SVGPath();
        p.setContent(svgPathData);
        p.setScaleX(0.85);
        p.setScaleY(0.85);
        return p;
    }

    private static String resolveTessdataPath() {
        String env = System.getenv("TESSDATA_PREFIX");
        if (env != null && !env.isBlank()) return env;
        Path local = Path.of(System.getProperty("user.home"), ".translator-desktop", "tessdata");
        if (local.toFile().exists()) return local.toAbsolutePath().toString();
        return null;
    }

    private static String mapToTesseractLang(String code) {
        if (code == null || code.isBlank()) return "eng";
        if (code.startsWith("en")) return "eng";
        if (code.startsWith("am")) return "amh";
        if (code.startsWith("ar")) return "ara";
        if (code.startsWith("fr")) return "fra";
        if (code.startsWith("es")) return "spa";
        if (code.startsWith("de")) return "deu";
        if (code.startsWith("it")) return "ita";
        if (code.startsWith("pt")) return "por";
        if (code.startsWith("ru")) return "rus";
        if (code.startsWith("zh")) return "chi_sim";
        return "eng";
    }

    private static void copyToClipboard(String text) {
        ClipboardContent content = new ClipboardContent();
        content.putString(text == null ? "" : text);
        Clipboard.getSystemClipboard().setContent(content);
    }


    private static void speakText(String text, String langCode) throws IOException, InterruptedException {
        String normalized = (text == null ? "" : text)
                .replace("\r\n", "\n")
                .replace("\r", "\n")
                .replace('\n', ' ')
                .replaceAll("\\s+", " ")
                .trim();
        if (normalized.isEmpty()) return;

        String lc = (langCode == null ? "" : langCode.trim().toLowerCase());
        String voicePrefix = "";
        if (lc.startsWith("ar")) voicePrefix = "ar";
        else if (lc.startsWith("en")) voicePrefix = "en";
        else if (lc.startsWith("fr")) voicePrefix = "fr";
        else if (lc.startsWith("es")) voicePrefix = "es";
        else if (lc.startsWith("de")) voicePrefix = "de";
        else if (lc.startsWith("ru")) voicePrefix = "ru";

        String os = System.getProperty("os.name", "").toLowerCase();
        if (os.contains("win")) {
            ProcessBuilder pb = new ProcessBuilder(
                    "powershell.exe",
                    "-NoProfile",
                    "-Command",
                    "$ErrorActionPreference='Stop'; " +
                            "Add-Type -AssemblyName System.Speech; " +
                            "[Console]::InputEncoding=[Text.Encoding]::Unicode; " +
                            "$t=[Console]::In.ReadToEnd(); " +
                            "$s=New-Object System.Speech.Synthesis.SpeechSynthesizer; " +
                            (voicePrefix.isBlank() ? "" :
                                    ("$v=$s.GetInstalledVoices() | Where-Object { $_.VoiceInfo.Culture.Name -like '" + voicePrefix + "-*' } | Select-Object -First 1; " +
                                            "if (-not $v) { Write-Error 'No TTS voice installed for language: " + voicePrefix + ". Install a Windows Speech voice (Settings > Time & language > Language & region > Add a language > Speech)'; exit 3 }; " +
                                            "$s.SelectVoice($v.VoiceInfo.Name); ")) +
                            "$s.Rate=0; $s.Volume=100; " +
                            "$s.Speak($t)"
            );
            pb.redirectErrorStream(true);
            Process p = pb.start();
            try (OutputStreamWriter w = new OutputStreamWriter(p.getOutputStream(), StandardCharsets.UTF_16LE)) {
                w.write(normalized);
            }
            int code = p.waitFor();
            String msg = "";
            try {
                byte[] bytes = p.getInputStream().readAllBytes();
                msg = bytes == null ? "" : new String(bytes, StandardCharsets.UTF_8).trim();
            } catch (IOException ignored) {
            }
            if (code != 0) {
                if (msg.isBlank()) throw new IOException("TTS failed (code " + code + ")");
                throw new IOException(msg);
            }
            return;
        }
        if (os.contains("mac")) {
            int code = new ProcessBuilder("say", normalized).start().waitFor();
            if (code != 0) throw new IOException("TTS failed (code " + code + ")");
            return;
        }
        int code = new ProcessBuilder("espeak", normalized).start().waitFor();
        if (code != 0) throw new IOException("TTS failed (code " + code + ")");
    }

    private static List<Path> resolveVoskModelCandidates() {
        List<Path> out = new ArrayList<>();

        String env = System.getenv("VOSK_MODEL_PATH");
        if (env != null && !env.isBlank()) {
            collectVoskModelCandidates(out, Path.of(env));
        }

        String userProfile = System.getenv("USERPROFILE");
        if (userProfile != null && !userProfile.isBlank()) {
            collectVoskModelCandidates(out, Path.of(userProfile, ".translator-desktop", "vosk-model"));
        }

        collectVoskModelCandidates(out, Path.of(System.getProperty("user.home"), ".translator-desktop", "vosk-model"));
        return out;
    }

    private static void collectVoskModelCandidates(List<Path> out, Path folder) {
        try {
            if (folder == null || !Files.exists(folder) || !Files.isDirectory(folder)) return;
            if (isVoskModelDir(folder)) out.add(folder);
            try (java.nio.file.DirectoryStream<Path> ds = Files.newDirectoryStream(folder)) {
                for (Path child : ds) {
                    if (child != null && Files.isDirectory(child) && isVoskModelDir(child)) {
                        out.add(child);
                    } else if (child != null && Files.isDirectory(child)) {
                        try (java.nio.file.DirectoryStream<Path> ds2 = Files.newDirectoryStream(child)) {
                            for (Path grandChild : ds2) {
                                if (grandChild != null && Files.isDirectory(grandChild) && isVoskModelDir(grandChild)) {
                                    out.add(grandChild);
                                }
                            }
                        } catch (IOException ignored) {
                        }
                    }
                }
            }
        } catch (IOException ignored) {
        }
    }

    private static boolean isVoskModelDir(Path dir) {
        if (dir == null) return false;
        if (Files.exists(dir.resolve("conf")) && Files.isDirectory(dir.resolve("conf"))) return true;
        if (Files.exists(dir.resolve("am")) && Files.isDirectory(dir.resolve("am"))) return true;
        return Files.exists(dir.resolve("conf").resolve("model.conf"))
                || Files.exists(dir.resolve("am").resolve("final.mdl"));
    }

    private static Path recordWavSeconds(int seconds) throws LineUnavailableException, IOException {
        AudioFormat format = new AudioFormat(16000f, 16, 1, true, false);
        DataLine.Info info = new DataLine.Info(TargetDataLine.class, format);
        TargetDataLine line = (TargetDataLine) AudioSystem.getLine(info);
        line.open(format);
        line.start();

        Path wav = Files.createTempFile("translator-voice-", ".wav");
        try (AudioInputStream ais = new AudioInputStream(line)) {
            long framesToCapture = (long) (format.getFrameRate() * seconds);
            AudioInputStream limited = new AudioInputStream(ais, format, framesToCapture);
            AudioSystem.write(limited, AudioFileFormat.Type.WAVE, wav.toFile());
        } finally {
            line.stop();
            line.close();
        }
        return wav;
    }

    private static String runVoskOnMic(List<Path> modelCandidates, int seconds) throws Exception {
        Path wav = recordWavSeconds(seconds);
        try {
            Exception last = null;
            for (Path modelPath : modelCandidates) {
                try (AudioInputStream ais = AudioSystem.getAudioInputStream(wav.toFile())) {
                    AudioFormat target = new AudioFormat(16000f, 16, 1, true, false);
                    try (AudioInputStream pcm = AudioSystem.getAudioInputStream(target, ais);
                         Model model = new Model(modelPath.toString());
                         Recognizer rec = new Recognizer(model, 16000f)) {

                        byte[] buffer = new byte[4096];
                        int n;
                        while ((n = pcm.read(buffer)) >= 0) {
                            rec.acceptWaveForm(buffer, n);
                        }
                        String json = rec.getFinalResult();
                        return extractVoskText(json);
                    }
                } catch (Exception ex) {
                    last = ex;
                }
            }
            if (last != null) throw last;
            return "";
        } finally {
            try { Files.deleteIfExists(wav); } catch (Exception ignore) {}
        }
    }

    private static String extractVoskText(String json) {
        if (json == null) return "";
        int idx = json.indexOf("\"text\"");
        if (idx < 0) return "";
        int colon = json.indexOf(':', idx);
        if (colon < 0) return "";
        int firstQuote = json.indexOf('"', colon + 1);
        if (firstQuote < 0) return "";
        int secondQuote = json.indexOf('"', firstQuote + 1);
        if (secondQuote < 0) return "";
        return json.substring(firstQuote + 1, secondQuote).trim();
    }

    public static void main(String[] args) {
        if (args.length > 0 && "--server".equals(args[0])) {
            runServer();
        } else {
            launch(args);
        }
    }

    private static void runServer() {
        try {
            String ltUrl = "http://127.0.0.1:5000";
            Registry reg = LocateRegistry.createRegistry(1099);
            reg.rebind("TranslationService", new com.translator.rmi.server.RmiTranslationRemoteImpl(ltUrl));
            System.out.println("RMI Server started on port 1099");
            Thread.currentThread().join();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
