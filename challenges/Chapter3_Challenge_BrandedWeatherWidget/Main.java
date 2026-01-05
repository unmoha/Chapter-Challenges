import javafx.animation.FadeTransition;
import javafx.application.Application;
import javafx.beans.property.SimpleStringProperty;
import javafx.beans.property.StringProperty;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import javafx.util.Duration;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) {
        // Properties for binding
        StringProperty tempProp = new SimpleStringProperty("25°C");

        // UI Components
        Label city = new Label("New York - Aero Dynamics");
        Label temp = new Label();
        temp.textProperty().bind(tempProp); // Property binding
        Label desc = new Label("Sunny Skies");
        VBox center = new VBox(temp, desc);

        Label forecast1 = new Label("Mon: 22°C");
        Label forecast2 = new Label("Tue: 24°C");
        HBox forecast = new HBox(forecast1, forecast2);

        BorderPane root = new BorderPane();
        root.setTop(city);
        root.setCenter(center);
        root.setBottom(forecast);

        // Animation
        FadeTransition ft = new FadeTransition(Duration.seconds(2), forecast);
        ft.setFromValue(0);
        ft.setToValue(1);
        ft.play();

        Scene scene = new Scene(root, 400, 300);
        scene.getStylesheets().add("styles.css"); // External CSS
        primaryStage.setTitle("Aero Dynamics Weather Widget");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}