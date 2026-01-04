import java.applet.Applet;
import java.awt.*;
public class BouncingTextApplet extends Applet implements Runnable {
    private int x = 0;
    private Thread t;
    private String text = "Anu Moha";

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
            x += 5;
            if (x > getWidth()) x = 0;
            repaint();
            try { Thread.sleep(100); } catch (InterruptedException e) {}
        }
    }

    public void paint(Graphics g) {
        g.drawString(text, x, 50);
    }
}