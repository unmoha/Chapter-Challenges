import java.applet.Applet;
import java.awt.*;

public class BouncingTextApplet extends Applet implements Runnable {
    private int x = 0; // Text position
    private Thread t; // Animation thread
    private String text = "Anu Moha"; // Text to display

    public void init() {
        setSize(400, 100);
        setBackground(Color.BLACK);
        setForeground(Color.GREEN);
    }

    public void start() {
        t = new Thread(this);
        t.start();
    }

    public void run() {
        while (true) {
            x += 5; // Move text right
            if (x > getWidth()) x = 0; // Reset if off-screen
            repaint();
            try {
                Thread.sleep(100); // Pause for animation
            } catch (InterruptedException e) {}
        }
    }

    public void paint(Graphics g) {
        g.drawString(text, x, 50); // Draw text
    }
}